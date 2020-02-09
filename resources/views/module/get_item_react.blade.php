@extends('layouts.app_blank')
@section('content')
    <?php

    use  \App\Classes\Utils\UtilsCommon;

    ob_start();
    $front_public_domain = config('common.amod_front_public_domain');
    $front_version = getFrontVersion();
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/jquery-1.8.1.min.js');
    \App\Classes\Utils\FrontBuilder::pushCss(config('common.public_ice_dist_url') . 'build/css/index.css?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs(config('common.public_ice_dist_url') . 'build/library/js/block.js?v=' . $front_version);
    $module_unique_id = 1001;
    $module_id = 'shopModuleId' . $module_unique_id;
    if (isset($_GET['module_item_id']) && preg_match('/[A-Za-z]+[A-Za-z0-9_\-:]*/', $_GET['module_item_id'])) {
        $module_id = $_GET['module_item_id'];
    }
    if (true === $admin) {
        echo '<div id="page">';
        echo '<div data-dir="' . $project_name . '"  data-page="' . $page_name . '" data-ajax="true"   id="shopModuleId' . $module_unique_id . '"   class="J_TModule"  moduleID="' . $module_name . '" data-position="' . $position . '">';
    } else {
        echo '<div id="' . $module_id . '">';
    }
    $rjs = new \App\Classes\Utils\ComponentReactJS();
    $js_all = $rjs->init_js;
    $ret = UtilsCommon::getReactModuleHtml($rjs, $json_item, $page_location, 'shopModuleId' . $module_unique_id);
    if (false === $ret['error']) {
        $js_all .= $ret['data'];
    }
    if (true === $admin) {
        echo '</div>';
        echo '</div>';
    } else {
        echo '</div>';
    }
    $logic_html = ob_get_contents();
    ob_end_clean();
    $logic_js = '';
    $module_type_name = 'react';
    UtilsCommon::modulePageView($design, $admin, $app_name, $module_type_name, $logic_html, $logic_js);
    ?>
    <script>
        <?php
        echo $js_all;
        $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
        echo '$("#' . $module_id . '").html(rendorBox["' . $key . '"]);';
        ?>
    </script>
    <?php
    ?>
@endsection
@section('css')
    <?php
    echo \App\Classes\Utils\FrontBuilder::showAllCss();
    ?>
@endsection
