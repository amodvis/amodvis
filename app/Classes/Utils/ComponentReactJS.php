<?php

namespace App\Classes\Utils;
/**!
 * Copyright (c) 2014, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree. An additional grant
 * of patent rights can be found in the PATENTS file in the same directory.
 */

/**
 * A PHP class to render React components on the server
 * and then hook the components on the client side.
 * Requires V8JS PHP extension: http://php.net/v8js
 */
class ComponentReactJS
{

    private
        /**
         * Name of the component to render
         * @var string
         */
        $component,

        /**
         * Properties that go along with the component
         * @var mixed
         */
        $data = null,

        /**
         * Instance of V8Js class
         * @var object
         */
        $v8,

        /**
         * Custom error handler
         * @var callable
         */
        $errorHandler;
    public $init_js = '';


    public static $env = 'v8';

    /**
     * Initialize by passing JS code as a string.
     * The application source code is concatenated string
     * of all custom components and app code
     * ComponentReactJS constructor.
     */
    function __construct()
    {
        $react = [];
        if (class_exists('\V8Js')) {
            self::$env = getOriginEnv('REACT_RUN_ENV') ?? 'v8';
        } else {
            self::$env = 'window';
        }
        if ('v8' === self::$env) {
            $lib_js_content = file_get_contents(config('common.public_ice_dist_url') . 'public/js/react-bundle.min.js');
            $app_js_content = file_get_contents(config('common.public_ice_dist_url') . 'build/library/js/block.js');
            $react[] = "var console = {warn: function(){}, error: print,debug:function(){},log:function(){}};";
            $react[] = "var global = global || this, self = self || this, window = window || this;";
            $react[] = $lib_js_content;
            $react[] = "var React = global.React, ReactDOM = global.ReactDOM, ReactDOMServer = global.ReactDOMServer;";
            $react[] = $app_js_content;
        }
        $concatenated = implode(";\n", $react);
        $this->init_js = $concatenated;
        if ('v8' === self::$env) {
            $this->v8 = new \V8Js();
        }
    }

    /**
     * Custom error handler. The default one var_dumps the exception
     * and die()s.
     *
     * @param callable $err Callback passed to call_user_func()
     * @return object $this instance
     */
    function setErrorHandler($err)
    {
        $this->errorHandler = $err;
        return $this;
    }

    /**
     * Returns the markup to print to the page
     * @param $name
     * @param $page_location
     * @param $prop
     * @return string
     */
    function getComponentHtmlAdvance($json_item, $name, $prop, $page_location, callable $beforeCallback)
    {
        $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
        if ('v8' === self::$env) {
            $global_var = 'global';
        } else {
            $global_var = 'window';
        }
        $js = sprintf(
            '
            
            try{
                if(typeof rendorBox == "undefined"){
                     ' . $global_var . '.rendorBox = {};
                }
                var component = React.createElement(%s, %s);
                var render = function(props){ return component;};
                var routeElement =   React.createElement(Route,{ "key":"' . $page_location . '","path":"' . $page_location . '"
              ,"render":render});
                var switchWrap=React.createElement(Switch, {},routeElement);
                 ' . $global_var . '.rendorBox["' . $key . '"] = ReactDOMServer.renderToString(React.createElement(StaticRouter, {"location":"' . $page_location . '"},switchWrap));
             }catch(err){
                var txt="此页面存在一个错误。\n\n"
                txt+="错误描述: " + err.description + "\n\n";
                console.log(txt);
             }
             ',
            $name,
            json_encode(['module_data' => $prop]));
        $js = 'var StaticRouter=LIBRARY_BLOCKS.StaticRouter;var Route=LIBRARY_BLOCKS.Route;var Switch=LIBRARY_BLOCKS.Switch;' . $js;
        $js = $beforeCallback($js);
        return $js;
    }

    /**
     * Returns JS to be inlined in the page (without <script> tags)
     * It instantiates the client side, once the JS arrives
     *
     * NOTE: This class makes no attempt to load files JS so you can load it
     * however is appropriate - from a CDN, asynchronously, etc.
     *
     * e.g. getJS('document.body');
     *     renders in body and doesn't retain a var
     * e.g. getJS('#page', "GLOB");
     *      renders in element id="page" and assigns the component to
     *      a JavaScript variable named GLOB for later use if needed
     * @param string $where A reference to a DOM object, or an ID
     *               for convenience if prefixed by a #. E.g. "#page"
     *               It will be passed to document.getElementById('page')
     * @param string $return_var Optional name of JS variable to be assigned to
     *               the rendered component
     * @return string JS code
     */
    function getJS($where, $return_var = null)
    {
        // special case for IDs
        if (substr($where, 0, 1) === '#') {
            $where = sprintf(
                'document.getElementById("%s")',
                substr($where, 1)
            );
        }
        return
            ($return_var ? "var $return_var = " : "") .
            sprintf(
                "ReactDOM.render(React.createElement(%s, %s), %s);",
                $this->component,
                $this->data,
                $where
            );
    }

    /**
     * Executes Javascript using V8JS, with primitive exception handling
     *
     * @param string $js JS code to be executed
     * @return string The execution response
     */
    public function executeJS($js)
    {
        try {
            ob_start();
            $this->v8->executeString($js);
            $ret = ob_get_clean();
            return $ret;
        } catch (\Exception $e) {
            if ($this->errorHandler) {
                call_user_func($this->errorHandler, $e);
            } else {
                echo $e->getMessage();
            }
            return '';
        }
    }

}
