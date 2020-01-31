@extends('layouts.app')
@section('title')
    <?php echo ($app_info->app_name_cn ?: $app_info->app_name) . '的预览';?>
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
                    src="<?php
                    $front_base_url = rtrim(getOriginEnv('AMOD_FRONT_BASE_URL'), '/');
                    $app_domain = $app_info->app_domain ? 'https://' . $app_info->app_domain : $front_base_url;
                    echo $app_domain . '/';
                    ?><?php echo $page_path;?>?vendor_id_by_domain=<?php echo $login_vendor_id;?>&app_name_by_domain=<?php echo $app_name;?>"></iframe>
        </div>
        <div class="pop_right">
            <div class="item_i return_page_list"><a href="/pages_info/<?php echo $app_name;?>/">返回页面列表</a></div>
            <div class="item_i mobile_view"><a
                        href="/page_builder_mobile/<?php echo $app_name;?>/<?php echo $page_path;?>/">返回页面编辑</a></div>
        </div>
    </div>
    <?php
    use  \App\Classes\Utils\UtilsCommon;
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


