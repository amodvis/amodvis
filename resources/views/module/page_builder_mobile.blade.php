@extends('layouts.app')
@section('title')
    <?php echo ($app_info->app_name_cn ?: $app_info->app_name) . '的可视化后台';?>
@endsection
@section('head')
    <style>
        .page_build_iframe {
            width: 375px;
            height: 900px;
            border: none;
            display: block;
        }

        .page_build_iframe_bd {
            width: 375px;
            height: 900px;
            margin: 0 auto;
            position: relative;
            border: 1px solid #b2d5ff;
        }

        .page_build_iframe_bd .pop_right {
            position: absolute;
            right: -50px;
            top: -1px;
            width: 50px;
            height: 300px;
        }

        .page_build_iframe_bd .item_i {
            background: #6da6ff;
            text-align: center;
            color: #fff;
            margin-bottom: 10px;
            display: inline;
            writing-mode: vertical-lr;
            padding: 5px 0;
            cursor: pointer;
        }

        .page_build_iframe_bd .item_i a, .page_build_iframe_bd .item_i a:hover {
            color: #ffffff;
            text-decoration: none;
        }

        .page_build_iframe_bd .return_page_list {
            position: absolute;
            left: 0;
            top: 0;
        }

        .page_build_iframe_bd .mobile_view {
            left: 0;
            top: 90px;
            position: absolute;
        }

    </style>
    <script>
        window.fromParentOpen = true;
    </script>
@endsection
@section('content')
    <div class="page_build_iframe_bd">
        <div class="main_bd">
            <iframe class="page_build_iframe" id="page_builder_iframe"
                    src="/page_builder/<?php echo $app_name;?>/<?php echo $page_name;?>?login_vendor_id=<?php echo $login_vendor_id;?>"></iframe>
        </div>
        <div class="pop_right">
            <div class="item_i return_page_list"><a href="/pages_info/<?php echo $app_name;?>/">返回页面列表</a></div>
            <div class="item_i mobile_view"><a  href="/page_mobile_view/<?php echo $app_name;?>/<?php echo $page_name;?>/">预览效果</a></div>
        </div>
    </div>
    <?php
    use  \App\Classes\Utils\UtilsCommon;
    use App\Classes\Services\Module\ModuleService;
    ob_start();
    $module_id_num = 100000;
    ?>
    <div id="page">
        <?php
        UtilsCommon::reduceModule($modules, function ($json_item)use (&$module_id_num) {
        $module_info = app(ModuleService::class)->getModuleInfoByCache($json_item['project_name'], $json_item['module_name']);
        ?>
        <div id="Md<?php echo $module_id_num;?>" style="min-height:auto;" class="J_TModule J_TEmptyBox J_ModuleOneSetup"
             data_module_no_reload=1
             data-dir="<?php echo $json_item['project_name']; ?>" data-page="<?php echo $json_item['page_name'];?>"
             moduleid="<?php echo $json_item['module_name'];?>" data-position="<?php echo $json_item['position'];?>"
             data_page_reload=0 data-flush_module=1
             module_nick_name="<?php echo($module_info->nick_name ?? $json_item['module_name'])?>">
            <a class="ds-bar-edit"
               href="javascript:void(0);"><span>&nbsp;&nbsp;</span></a>
        </div>
        <?php
        $module_id_num++;
        });
        ?>
    </div>
    <?php
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
@endsection


