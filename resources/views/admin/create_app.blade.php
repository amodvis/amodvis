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
    <div class="module_one_setup">
        <div class="module_one_setup_items J_ModuleXmlSetup" data_height="320" data_width="500"
             data_form_action="<?php echo $form_action;?>" data_page_reload=1>
            <span class="J_UpsetOneXmlFormModule setup_btn">创建应用</span>
        </div>
    </div>
    <?php
    echo '<table class="fashiontable">';
    echo '<tr><th width="25%">APP简称</th><th width="25%">APP中文名</th><th width="25%">APP描述</th><th width="25%">操作</th></tr>';
    foreach ($app_list['data'] as $row) {
        echo '<tr>
        <td>' . $row->app_name . '</td>
        <td>' . ($row->app_name_cn ?? '') . '</td>
        <td>' . ($row->des ?? '') . '</td>
        <td>
        <div class="table_list_btns">
        <a href="javascript:void(0);" class="J_ModuleXmlSetup" data_height="320" data_width="500"
             data_form_action="' . $form_action . '" data_query="?app_name=' . $row->app_name . '" data_page_reload=1>
            <span class="J_UpsetOneXmlFormModule setup_btn pointer">基本信息修改</span>
        </a>
        <a href="/pages_info/' . $row->app_name . '/">APP管理</a>
        </div>
</td></tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '<div class="pagination_module">总共' . $app_list['total'] . '条记录</div>';
    $logic_html = ob_get_contents();
    ob_end_clean();
    $logic_js = '';
    $module_type_name = 'react';
    $app_name = '';
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
    </style>
@endsection
