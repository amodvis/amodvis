@extends('layouts.app')
@section('content')
    <?php

    use  \App\Classes\Utils\UtilsCommon;

    ob_start();
    $module_unique_id = 1001;
    if (true === $admin) {
        echo '<div id="page">';
        echo '<div data-dir="' . $project_name . '"  data-page="' . $page_name . '" data-ajax="true"   id="shopModuleId' . $module_unique_id . '"   class="J_TModule"  moduleID="' . $module_name . '" data-position="' . $position . '">';
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
    }
    $logic_html = ob_get_contents();
    ob_end_clean();
    $logic_js = '';
    $module_type_name = 'react';
    UtilsCommon::modulePageView($design, $admin, $module_type_name, $logic_html, $logic_js);
    ?>
    <script>
        <?php
        echo $js_all;
        $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
        echo '$("#shopModuleId' . $module_unique_id . '").html(rendorBox["' . $key . '"]);';
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
