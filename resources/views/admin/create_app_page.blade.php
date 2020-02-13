@extends('layouts.app')
@section('title')
    <?php echo $title ?? '';?>
@endsection
@section('content')
    <?php

    use  \App\Classes\Utils\UtilsCommon;

    ob_start();
    echo '<div id="page">';
    ?>
    <div class="app_module" style="color: #666;text-align: center;">
        当前应用:<?php echo $app_info->app_name_cn ?: $app_info->app_name;?></div>
    <div class="module_one_setup">
        <div class="module_one_setup_items J_ModuleXmlSetup" data_height="350" data_width="500"
             data_form_action="<?php echo $form_action;?>" data_page_reload=1>
            <span class="J_UpsetOneXmlFormModule setup_btn">新增页面</span>
        </div>
    </div>
    <?php
    echo '<table class="fashiontable">';
    echo '<tr><th width="25%">页面路径</th><th width="25%">页面标题</th><th width="25%">页面描述</th><th width="25%">操作</th></tr>';
    $front_base_url = rtrim(getOriginEnv('AMOD_FRONT_BASE_URL'), '/');
    if ($app_info->app_view_type == 2) {
        $edit_page = 'page_builder_mobile';
    } else {
        $edit_page = 'page_builder';
    }
    foreach ($page_list['data'] as $row) {
        if ($row->is_hide_page) {
            if (!request()->input('debug_pages')) {
                continue;
            }
        }
        $app_domain = $app_info->app_domain ? request()->getScheme() . '://' . $app_info->app_domain : $front_base_url;
        $page_link = $app_domain . '/' . str_replace('-', '/', trim($row->page_name, '/'));
        $tem_page_name = '/' . str_replace('-', '/', trim($row->page_name, '/'));
        $front_url = '';
        if (request()->input('debug_routes')) {
            $debug_routes = 1;
        } else {
            $debug_routes = 0;
        }
        $setting_html = '

<div class="table_list_btns">
<span class="J_ModuleXmlSetup" data_height="320" data_width="500"
 data_form_action="' . $form_action . '" data_query="?page_name=' . $row->page_name . '" data_page_reload=1>
<a href="javascript:void(0);" class="J_UpsetOneXmlFormModule setup_btn pointer">基本信息修改</a>
</span>
<span class="J_ModuleXmlSetup" data_height="420" data_width="500"
 data_form_action="' . $layout_form_action . $row->page_name . '?debug_routes=' . $debug_routes . '" data_query="" data_page_reload=0  data-page="' . $row->page_name . '">
<a href="javascript:void(0);" class="J_UpsetOneXmlFormModule setup_btn pointer">布局模块</a>
</span>
<a target="_blank" href="/' . $edit_page . '/' . $row->app_name . '/' . $row->page_name . '">页面编辑</a>
<a target="_blank" href="' . App\Classes\Utils\StringUtil::routeReplaceForTag($page_link) . '">访问页面</a>
</div>
';
        $page_type = $row->page_type;
        $tag_html = '';
        if (1 == $page_type) {
            $tag_html = '<i>系统页面</i>';
        }
        if (1 == $row->is_pre_load) {
            $tag_html .= '<i class="pre_load_page">预载入页</i>';
        }
        echo '<tr>
        <td>' . $tem_page_name . '</td>
        <td><div class="page_name_cn"><div class="item_main">' . ($row->page_name_cn ?? '') . '</div>' . $tag_html . '</div></td>
        <td>' . ($row->des ?? '') . '</td><td>' . $setting_html . '</td></tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '<div class="pagination_module">总共' . $page_list['total'] . '条记录</div>';
    $logic_html = ob_get_contents();
    ob_end_clean();
    $logic_js = '';
    $module_type_name = 'react';
    UtilsCommon::modulePageView($design, $admin, $app_name, $module_type_name, $logic_html, $logic_js);
    ?>

@endsection
@section('css')
    <?php
    echo \App\Classes\Utils\FrontBuilder::showAllCss();
    ?>
    <style>
        table.dataintable td {
            padding: 0;
        }

        .editPanel .control table .input-box {
            border: none;
            color: #666666;
        }

        .form-default .component_layout textarea {
            width: 400px;
            height: 180px;
            border: 1px solid #cccccc;
        }

        .page_name_cn {
            position: relative;
            width: 190px;
            margin: 0 auto;
            height: 18px;
            line-height: 18px;
        }

        .page_name_cn i {
            position: absolute;
            right: 0;
            top: -10px;
            background: #2b7af6;
            color: #fff;
            width: 50px;
            font-size: 11px;
            -webkit-transform: scale(0.7);
            height: 17px;
            padding: 2px 4px;
            line-height: 18px;
        }

        .page_name_cn .item_main {
            line-height: 18px;
        }

        .page_name_cn .pre_load_page {
            top: 8px;
            right: 0px;
            background: #4fa9ff;
        }
    </style>
    <script src="<?php
    $front_public_domain = config('common.amod_front_public_domain');
    echo $front_public_domain; ?>laravle-amodvis/amodvis/js/jquery-1.8.1.min.js"></script>
@endsection
@section('head')
    <?php
    if(true === $is_set_app_cookie){
    ?>
    <script>
        $.ajax({
            type: "GET",
            url: "<?php echo getOriginEnv('AMOD_FRONT_BASE_URL');?>app_front/vendor_bind/login/sso?shop_vendor_token=<?php echo $shop_vendor_token;?>",
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            success: function () {
            },
            error: function () {
            }
        });
    </script>
    <?php
    }
    ?>
@endsection
