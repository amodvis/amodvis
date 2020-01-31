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
class PageReactJS
{

    private
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

    /**
     * Initialize by passing JS code as a string.
     * The application source code is concatenated string
     * of all custom components and app code
     *
     */
    function __construct()
    {
        $this->v8 = new \V8Js();
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
     * Executes Javascript using V8JS, with primitive exception handling
     *
     * @param string $js JS code to be executed
     * @return string The execution response
     */
    public function executeJS($js)
    {
        try {
            ob_start();
            $window_append = 'window = {};
            window.navigator = {};
            window.screen = {};
            window.screen.height = 0;
            window.navigator.userAgent = "' . \Request::header('User-Agent') . '";
            ';
            $js = $window_append . $js;
            $this->v8->executeString($js, '', \V8Js::FLAG_FORCE_ARRAY);
            return ob_get_clean();
        } catch (\Exception $e) {
            if ($this->errorHandler) {
                call_user_func($this->errorHandler, $e);
            } else {
                // default error handler blows up bad
                echo "<pre>";
                echo $e->getMessage();
                echo "</pre>";
                die();
            }
        }
    }

}
