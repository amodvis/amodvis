<?php

namespace App\Classes\Utils;

use App\Classes\Services\Module\ModuleService;

use \App\Classes\Utils\ComponentReactJS as ComponentReactJS;

class UtilsCommon
{
    static $module_mappding = [];
    static $frame_global_sets = [];

    public static function getDomainByBaseUrl($base_url)
    {
        if (!$base_url) {
            return '';
        }
        return (parse_url($base_url))['host'];
    }

    public static function urlAddCookieEdit()
    {
        $backurl = $_SERVER["REQUEST_URI"];
        $parseUrl = parse_url($backurl);
        if (empty($parseUrl['query'])) {
            $splitStr = '?';
        } else {
            $splitStr = '&';
        }
        $backurl = rtrim($backurl, '?');
        $backurl .= $splitStr . 'cookie_edit=1';
        return $backurl;
    }

    public static function objectToArray($data): array
    {
        $obj = (array)$data;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return [];
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::objectToArray($v);
            }
        }
        return $obj;
    }


    public static function reduceModule($modules, callable $callback)
    {
        foreach ($modules as $page_part => $part_modules) {
            foreach ($part_modules as $type_key => $items) {
                foreach ($items as $json_items) {
                    foreach ($json_items as $json_item) {
                        $callback($json_item);
                    }
                }
            }
        }
    }

    public static function reduceModuleAndModOrigin(&$modules, callable $callback)
    {
        foreach ($modules as $page_part => &$part_modules) {
            foreach ($part_modules as $type_key => &$items) {
                foreach ($items as &$json_items) {
                    foreach ($json_items as &$json_item) {
                        $callback($json_item);
                    }
                }
            }
        }
    }

    /**
     * 批量获取页面模块数据
     * @param $shop_vendor_id
     * @param $app_name
     * @param $modules
     * @param bool $is_async
     * @return array
     */
    public static function getModulesData($shop_vendor_id, $app_name, $modules, $is_async = false)
    {
        $module_mapping = [];
        $modules = $modules ?: [];
        if (true === $is_async) {
            $get_module_api = config('common.amod_api_domain') . 'api/module_api/index/get_one_module_data/' . $app_name;
            $client = new \GuzzleHttp\Client();
            $count = count($modules);
            $cur_count = 0;
            $amodvis_vendor_token = HttpAuth::getAmodAuthorization($shop_vendor_id);
            foreach ($modules as $json_item) {
                $cur_count++;
                $options = [
                    'headers' => ['shop-vendor-token' => $amodvis_vendor_token],
                    'timeout' => 2,
                    'verify' => false
                ];
                $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                $module_mapping[$key] = [];
                $request_url = $get_module_api . '/' . $key;
                $promise = $client->getAsync($request_url, $options)->then(function ($response) use ($json_item, &$module_mapping) {
                    $ret = $response->getBody()->getContents();
                    $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                    $ret = json_decode($ret, true);
                    if (isset($ret['code']) && 0 === $ret['code']) {
                        $module_mapping[$key] = $ret['data'];
                    }
                });
                if ($count === $cur_count) {
                    $promise->wait();
                }
            }
        } else {
            foreach ($modules as $json_item) {
                $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                $widget_class_name = '\\Amvphp\\' . $json_item['project_name'] . '\\' . $json_item['module_name'] . '\\DefaultWidget';
                if (class_exists($widget_class_name)) {
                    $module_content = $widget_class_name::widget($shop_vendor_id, $app_name, $json_item);
                } else {
                    $module_content = \App\Classes\Widget\Widget::widget($shop_vendor_id, $app_name, $json_item);
                }
                $module_content = $module_content ?: [];
                $json_item['module_data'] = $json_item['module_data'] ?? [];
                $json_item['module_data'] = array_merge($json_item['module_data'], $module_content);
                $module_mapping[$key] = $json_item['module_data'];
            }
        }
        return $module_mapping;
    }

    /**
     * react视图
     * @param $user_id
     * @param $app_name
     * @param $options
     * @return array
     */
    public static function getReactPageModulesHtml($user_id, $app_name, $options)
    {
        $modules = $options['modules'];
        $admin = $options['admin'];
        $page_location = $options['page_location'];
        $module_unique_id = 1001;
        $content = '';
        $client = new \GuzzleHttp\Client();
        $count = 0;
        $page_modules = [];
        self::reduceModule($modules, function ($json_item) use (&$count, &$page_modules) {
            $page_modules[] = $json_item;
            unset($json_item);
            $count++;
        });
        $module_data_ret = self::getModulesData($user_id, $app_name, $page_modules, $options['is_advance']);
        $cur_count = 0;
        $rjs = app(ComponentReactJS::class);
        $js_all = $rjs->init_js;
        $module_mappding = [];
        self::reduceModule($modules, function ($json_item) use (
            &$cur_count,
            $count,
            $page_location,
            $client,
            $rjs,
            &$js_all,
            $user_id,
            $module_data_ret,
            &$module_mappding
        ) {
            $cur_count++;
            $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
            $module_content = $module_data_ret[$key] ?? [];
            $json_item['module_data'] = $json_item['module_data'] ?? [];
            $json_item['module_data'] = array_merge($json_item['module_data'], $module_content);
            $ret = \App\Classes\Utils\UtilsCommon::getReactModuleHtml($rjs, $json_item, $page_location);
            if (true === $ret['error']) {
                $module_mappding[$key] = $ret['data'];
            } else {
                $js_all .= $ret['data'];
            }
            if ($count === $cur_count) {
                if ('window' === ComponentReactJS::$env) {
                    $module_mappding = [];
                } else {
                    $js_all .= 'print(JSON.stringify(global.rendorBox))';
                    $execute_ret = $rjs->executeJS($js_all);
                    $execute_ret = json_decode($execute_ret, true);
                    $module_mappding = $execute_ret;
                }
            }
        });
        $module_id_mapping_key = [];
        foreach ($modules as $page_part => $part_modules) {
            $content .= '<div class="' . $page_part . '">';
            foreach ($part_modules as $i => $items) {
                if (count($items) > 1) {
                    $content .= '<div class="col_main">';
                }
                foreach ($items as $type_key => $json_items) {
                    $content .= '<div class="' . $type_key . '">';
                    foreach ($json_items as $json_item) {
                        $module_info = app(ModuleService::class)->getModuleInfoByCache($json_item['project_name'], $json_item['module_name']);
                        $json_item['module_data'] = $json_item['module_data'] ?? [];
                        $project_arr[] = ['project_name' => $json_item['project_name'], 'module_name' => $json_item['module_name']];
                        $content .= '<div data-dir="' . $json_item['project_name'] . '"  data-page="' . $json_item['page_name'] .
                            '" data-ajax="true" data-flush_module=1 id="shopModuleId' . $module_unique_id . '"   class="J_TModule style_module_' . $json_item['module_name'] . '"  moduleID="' . $json_item['module_name'] . '" data-position="' .
                            $json_item['position'] . '" module_nick_name="' . ($module_info->nick_name ?? $json_item['module_name']) . '">';
                        $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                        if ('window' === ComponentReactJS::$env) {
                            $module_id_mapping_key['shopModuleId' . $module_unique_id] = $key;
                        } else {
                            $content .= $module_mappding[$key];
                        }
                        $module_unique_id++;
                        $content .= '</div>';
                    }
                    $content .= '</div>';
                }
                if (count($items) > 1) {
                    $content .= '</div>';
                }
            }
            $content .= '</div>';
        }
        $content_ret = '';
        if (true === $admin) {
            $content_ret .= '<div id="page">';
        }
        $content_ret .= $content;
        if (true === $admin) {
            $content_ret .= '</div>';
        }
        if ('window' === ComponentReactJS::$env) {
            return [$content_ret, $js_all, $module_id_mapping_key];
        } else {
            return [$content_ret, '', []];
        }
    }

    /**
     * php视图 完成自定义layout
     * @param $options
     * @param $user_id
     * @return array
     */
    public static function getLayoutPageModulesHtml($options, $user_id)
    {
        $content = '';
        $project_arr = [];
        $layout_template = $options['layout_template'];
        $layout_json = $options['layout_json'];
        $admin = $options['admin'];
        $design = $options['design'];
        foreach ($layout_template as $key => $template_item) {
            $tem_layout_json = json_decode($layout_json[$key], true);
            if (!$tem_layout_json) {
                $content .= $template_item;
                continue;
            }
            $initBox = [];
            foreach ($tem_layout_json as $type_key => $items) {
                $module_content = '';
                foreach ($items as $json_item) {
                    $project_arr[] = ['project_name' => $json_item['project_name'], 'module_name' => $json_item['module_name']];
                    $widget_class_name = '\\Amvphp\\' . $json_item['project_name'] . '\\' . $json_item['module_name'] . '\\DefaultWidget';
                    $project_common = '\\Amvphp\\' . $json_item['project_name'] . '\\UtilsCommon';
                    if (true === $design) {
                        if (empty($initBox[$project_common])) {
                            $initBox[$project_common] = true;
                            (new $project_common())->init();
                        }
                    }
                    $base_url = $project_common::$static_base;
                    if (true === $design) {
                        \App\Classes\Utils\FrontBuilder::pushCss($base_url . '/modules/' . $json_item['module_name'] . '/stylesheet/default.css?v=' . getFrontVersion());
                        \App\Classes\Utils\FrontBuilder::pushJs($base_url . '/modules/' . $json_item['module_name'] . '/js/default.js?v=' . getFrontVersion());
                    }
                    if (true === $admin) {
                        $module_info = app(ModuleService::class)->getModuleInfoByCache($json_item['project_name'], $json_item['module_name']);
                        $module_content .= '<div data-dir="' . $json_item['project_name'] . '"  data-page="' . $json_item['project_name'] .
                            '" data-ajax="true"   id="shopModuleId' . time() . rand(1, 999999) . '"   class="J_TModule style_module_' . $json_item['module_name'] . '"  moduleID="' . $json_item['module_name'] . '" data-position="' .
                            $json_item['position'] . '"  module_nick_name="' . ($module_info->nick_name ?? $json_item['module_name']) . '">';
                    }
                    $module_content .= $widget_class_name::widget($json_item, $user_id);
                    if (true === $admin) {
                        $module_content .= '</div>';
                    }
                }
                $template_item = str_replace('[[-' . $type_key . ']]', $module_content, $template_item);
            }
            $content .= $template_item;
        }
        $content_ret = '';
        if (true === $admin) {
            $content_ret .= '<div id="page">';
        }
        $content_ret .= $content;
        if (true === $admin) {
            $content_ret .= '</div>';
        }
        return [$content_ret, $project_arr];
    }

    /**
     * php视图
     * @param $user_id
     * @param $app_name
     * @param $options
     * @return array
     */
    public static function getCommonPageModulesHtml($user_id, $app_name, $options)
    {
        $modules = $options['modules'];
        $admin = $options['admin'];
        $design = $options['design'];
        $module_unique_id = 1001;
        $content = '';
        $project_arr = [];
        $count = 0;
        $page_modules = [];
        $initBox = [];
        self::reduceModule($modules, function ($json_item) use (&$count, &$page_modules) {
            $page_modules[] = $json_item;
            unset($json_item);
            $count++;
        });
        $module_data_ret = self::getModulesData($user_id, $app_name, $page_modules, true);
        foreach ($modules as $page_part => $part_modules) {
            $content .= '<div class="' . $page_part . '">';
            foreach ($part_modules as $i => $items) {
                if (count($items) > 1) {
                    $content .= '<div class="col_main">';
                }
                foreach ($items as $type_key => $json_items) {
                    $content .= '<div class="' . $type_key . '">';
                    foreach ($json_items as $json_item) {
                        $project_arr[] = ['project_name' => $json_item['project_name'], 'module_name' => $json_item['module_name']];
                        $widget_class_name = '\\Amvphp\\' . $json_item['project_name'] . '\\' . $json_item['module_name'] . '\\DefaultWidget';
                        $project_common = '\\Amvphp\\' . $json_item['project_name'] . '\\Common';
                        if (empty($initBox[$project_common])) {
                            $initBox[$project_common] = true;
                            (new $project_common())->init();
                        }
                        $base_url = $project_common::$static_base;
                        if (true === $design) {
                            \App\Classes\Utils\FrontBuilder::pushCss($base_url . '/modules/' . $json_item['module_name'] . '/stylesheet/default.css?v=' . getFrontVersion());
                            \App\Classes\Utils\FrontBuilder::pushJs($base_url . '/modules/' . $json_item['module_name'] . '/js/default.js?v=' . getFrontVersion());
                        }
                        if (true === $admin) {
                            $module_info = app(ModuleService::class)->getModuleInfoByCache($json_item['project_name'], $json_item['module_name']);
                            $content .= '<div data-dir="' . $json_item['project_name'] . '"  data-page="' . $json_item['project_name'] .
                                '" data-ajax="true"   id="shopModuleId' . $module_unique_id . '"   class="J_TModule style_module_' . $json_item['module_name'] . '"  moduleID="' . $json_item['module_name'] . '" data-position="' .
                                $json_item['position'] . '"  module_nick_name="' . ($module_info->nick_name ?? $json_item['module_name']) . '">';
                            $module_unique_id++;
                        }
                        $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                        $json_item['module_data'] = $module_data_ret[$key];
                        if (isset($options['kissy_use_mapping']) && !empty($options['kissy_use_mapping'][$json_item['project_name'] . '/' . $json_item['module_name']])) {
                            $json_item['kissy_use'] = $options['kissy_use_mapping'][$json_item['project_name'] . '/' . $json_item['module_name']];
                        }
                        $content .= $widget_class_name::widget($user_id, $app_name, $json_item);
                        if (true === $admin) {
                            $content .= '</div>';
                        }
                    }
                    $content .= '</div>';
                }
                if (count($items) > 1) {
                    $content .= '</div>';
                }
            }
            $content .= '</div>';
        }
        $content_ret = '';
        if (true === $admin) {
            $content_ret .= '<div id="page">';
        }
        $content_ret .= $content;
        if (true === $admin) {
            $content_ret .= '</div>';
        }
        return [$content_ret, $project_arr];
    }

    /**
     * REACT单个模块HTML获取
     * @param ComponentReactJS $rjs
     * @param $json_item
     * @param string $page_location
     * @return array
     */
    public static function getReactModuleHtml(ComponentReactJS $rjs, $json_item, $page_location = '')
    {
        $block_name = $json_item['module_name'];
        $project_name = $json_item['project_name'];
        $json_item['module_data'] = $json_item['module_data'] ?: [];
        try {
            $ret = $rjs->getComponentHtmlAdvance(
                $json_item,
                $block_name,
                $json_item['module_data'],
                $page_location,
                function ($content) use ($project_name, $block_name) {
                    $append_content = '
                eval("var ' . $block_name . '=LIBRARY_BLOCKS[\'' . $project_name . '\'][\'' . $block_name . '\'];");
                ';
                    $ret = '(function(){' .
                        $append_content .
                        $content
                        . '})();';
                    return $ret;
                }
            );
            return ['error' => false, 'data' => $ret];
        } catch (\exception $e) {
            $ret = '模块HTML获取异常，请检查模块数据:' . $e->getMessage();
            return ['error' => true, 'data' => $ret];
        }
    }

    /**
     * PHP单个模块HTML获取
     * @param $json_item
     * @param $user_id
     * @return mixed
     */
    public static function getCommonModuleHtml($json_item, $user_id)
    {
        $project_name = $json_item['project_name'];
        $module_name = $json_item['module_name'];
        $page_name = $json_item['page_name'];
        $position = $json_item['position'];
        $project_common = '\\Amvphp\\' . $project_name . '\\UtilsCommon';
        (new $project_common())->init();
        $class_name = '\\Amvphp\\' . $project_name . '\\' . $module_name . '\\DefaultWidget';
        $view['class_name'] = $class_name;
        $options = [
            'project_name' => $project_name,
            'module_name' => $module_name,
            'page_name' => $page_name,
            'position' => $position,
        ];
        return $class_name::widget($options, $user_id);
    }

    public static function modulePageView($design, $admin, $app_name, $module_type_name, $logic_html, $logic_js)
    {
        $front_public_domain = config('common.amod_front_public_domain');
        $combine = getOriginEnv('KISSY_BASIC_COMBINE') ? 1 : 0;
        $front_version = getFrontVersion();
        if (true === $design) {
            if (true === $admin) {
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/design.css?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/debug.css?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/common.css?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/module_components.css?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/uploadzone/public/stylesheet/default.css?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/uploadzone/modules/top_part/stylesheet/default.css?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/uploadzone/modules/file_list/stylesheet/default.css?v=' . $front_version);
            }
        }

        echo $logic_html;

        if (true === $design) {
            if (true === $admin) {
                if ($combine) {
                    \App\Classes\Utils\FrontBuilder::pushJs(getOriginEnv('KISSY_COMBINE_BASE_URL') . 'kissy/k/1.4.8/??seed.js,import-style.js');
                } else {
                    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/ks/ks-1.4.7/build/seed.js');
                }
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/helper.js?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/pages_class.js?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/jquery-1.8.1.min.js');
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/uploadzone/modules/top_part/js/default.js?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/uploadzone/modules/file_list/js/default.js?v=' . $front_version);
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/kindeditor.js');
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/lang/zh_CN.js');
                \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/plugins/code/prettify.js');
            }
            echo \App\Classes\Utils\FrontBuilder::showAllJs();
            echo $logic_js;
        }
        if (true === $admin) {
            $amod_api_domain = config('common.amod_api_cms_domain');
            $app_name = $app_name ?? '';
            $amod_js_domain = config('common.amod_js_domain');
            print <<<EOT
    <script>
    if(typeof webConfig==="undefined"){
        window.webConfig = {};
    }
    webConfig.module_debug = true;
    webConfig.module_tag = '';
    webConfig.isFromShop = true;
    webConfig.previewUser= '';
    webConfig.is_edit_one_module = true;
    webConfig.module_type_name = "{$module_type_name}";
    webConfig.app_name = "{$app_name}";
    var PUBLIC_URL = "{$front_public_domain}laravle-amodvis/amodvis/";
    var FRONT_DOMAIN = "{$front_public_domain}";
    var API_URL = "{$amod_api_domain}api/";
    var ADMIN_URL = "{$amod_api_domain}api/";
    var UPLOAD_URL = "{$amod_api_domain}api/";
     var JS_DOMAIN  = "{$amod_js_domain}";
    var srcPath = PUBLIC_URL + "js/ks";
    var moduleBuildPath = PUBLIC_URL + "js/ks/build/modules";
    KISSY.config({
        combine: !!parseInt({$combine}),
        packages: {
            "kg/slide/2.0.0/base" : {
                 base: srcPath+'/gallery/slide/2.0.2/build/base',
                tag:'{$front_version}',
                ignorePackageNameInUri:true
            },
            "kg/calendar/2.0.2/index" : {
                base: srcPath+'/gallery/calendar/2.0.2/build/index',
                tag:'{$front_version}',
                ignorePackageNameInUri:true
            },
            "kg/datetimepicker/2.0.0/index" : {
                base: srcPath+'/gallery/datetimepicker/2.0.0/build/index',
                tag:'{$front_version}',
                ignorePackageNameInUri:true
            },
            "modules":{
                    base: moduleBuildPath,
                    charset: "utf-8",
                    tag: "{$front_version}",
                    combine: false,
                    ignorePackageNameInUri:true
             }
        }
    });
    KISSY.use("modules/module_init/module_edit",function(S,ModuleEdit){
        ModuleEdit.init();
    });
    KISSY.use("modules/module_init/page_edit",function(S,PageEdit){
        PageEdit.init();
    });
    KISSY.use("modules/module_init/init_widget");
    KISSY.use("modules/module_components/calendar");
    KISSY.use("modules/module_components/datetimepicker");
    KISSY.use("modules/module_init/module_drag",function(S,DragModule){
        DragModule.init();
    });
    </script>
EOT;
        }
    }

    public
    static function frameGlobalSet($key, $value)
    {
        request()->request->set($key, $value);
        self::$frame_global_sets[$key] = $key;
    }

    public
    static function getModulePost()
    {
        $post_data = request()->input();
        foreach ($post_data as $key => $name) {
//            if ('__' === substr($key, 0, 2)) {
//                unset($post_data[$key]);
//            }
        }
        foreach (self::$frame_global_sets as $name) {
            unset($post_data[$name]);
        }
        return $post_data;
    }

    /**
     * 简单非JSON提交方案
     * @return array
     */
    public
    static function getPostData()
    {
        $headercontent = file_get_contents("php://input");
        $headercontentarr = explode("&", $headercontent);
        $_BAIPOST = array();
        foreach ($headercontentarr as $value) {
            $tarr = explode("=", $value);
            if (!isset($_BAIPOST[urldecode($tarr[0])])) {
                $_BAIPOST[urldecode($tarr[0])] = urldecode($tarr[1]);
            } else {
                $_BAIPOST[urldecode($tarr[0])] = $_BAIPOST[urldecode($tarr[0])] . '@_@' . urldecode($tarr[1]);
            }
        }
        return $_BAIPOST;
    }

    /**
     * 删除反斜杠
     */
    public
    static function moveSlashes($arr)
    {
        if (get_magic_quotes_gpc()) {
            $arr = is_array($arr) ? array_map('stripslashes', $arr) : stripslashes($arr);
        }
        return $arr;
    }

    public
    static function getDirList($dir)
    {
        $ret = [];
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) != false) {
                    if ('.' === substr($file, 0, 1)) {
                        continue;
                    }
                    if (!preg_match("/^[a-zA-Z0-9_]+$/", $file)) {
                        continue;
                    }
                    //文件名的全路径 包含文件名
                    $file_path = $dir . $file;
                    //获取文件修改时间
                    $fmt = filemtime($file_path);
                    $item = [
                        'update_time' => date("Y-m-d H:i:s", $fmt),
                        'module_name' => $file
                    ];
                    $ret[] = $item;
                }
                array_multisort(array_column($ret, 'module_name'), SORT_DESC, $ret);
                closedir($dh);
            }
        }
        return $ret;
    }
}
