@extends('layouts.app')
@section('content')
    <?php

    use  \App\Classes\Utils\UtilsCommon;

    ob_start();
    if (true === $admin) {
        echo '<div id="page">';
        echo '<div data-dir="' . $project_name . '"  data-page="' . $page_name . '" data-ajax="true"   id="shopModuleId' . time() . rand(1, 999999) . '"   class="J_TModule"  moduleID="' . $module_name . '" data-position="' . $position . '">';
    }
    echo UtilsCommon::getCommonModuleHtml($json_item, $login_vendor_id);
    if (true === $admin) {
        echo '</div>';
        echo '</div>';
    }
    $logic_html = ob_get_contents();
    ob_end_clean();
    $logic_js = '<script>
            JuDianCommon_' . $project_name . '.' . $module_name . '.init();
        </script >';
    $module_type_name = 'common';
    UtilsCommon::modulePageView($design, $admin, $app_name, $module_type_name, $logic_html, $logic_js);
    ?>
@endsection
@section('content')
@section('css')
    <?php
    echo \App\Classes\Utils\FrontBuilder::showAllCss();
    ?>
@endsection
