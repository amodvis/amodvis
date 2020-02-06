@extends('layouts.app_blank')
@section('title')
    <?php echo ($app_info->app_name_cn ?: $app_info->app_name) . '的可视化后台';?>
@endsection
@section('css')
    <?php
    $front_public_domain = config('common.amod_front_public_domain');
    $front_base_url = rtrim(getOriginEnv('AMOD_FRONT_BASE_URL'), '/');
    $app_domain = $app_info->app_domain ? 'https://' . $app_info->app_domain : $front_base_url;
    $front_version = getFrontVersion();
    ?>
    <link href="<?php echo config('common.public_ice_dist_url') ?>build/css/index.css?v=<?php echo $front_version;?>"
          rel="stylesheet">
    <?php
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
        echo \App\Classes\Utils\FrontBuilder::showAllCss();
    }
    ?>
    <style>
        .ks-popup-content *,
        .ks-popup-content ::before,
        .ks-popup-content ::after {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
        }
    </style>
    <?php echo $app_info->head_content ?? ''; ?>
    <?php echo $app_page_info->head_content ?? ''; ?>
@endsection
@section('content')
    <?php
    use App\Classes\Utils\UtilsCommon;
    $combine = getOriginEnv('KISSY_BASIC_COMBINE') ? 1 : 0;
    $project_arr = [];
    list($content, $js_all, $module_id_mapping_key) = UtilsCommon::getReactPageModulesHtml($login_vendor_id, $app_name, $page_modules_html_options);
    echo $content;
    if (true === $design) {
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/jquery-1.8.1.min.js');
    echo \App\Classes\Utils\FrontBuilder::showAllJs();
    if ($combine) {
        \App\Classes\Utils\FrontBuilder::pushJs(getOriginEnv('KISSY_COMBINE_BASE_URL') . 'kissy/k/1.4.8/??seed.js,import-style.js');
    } else {
        \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/ks/ks-1.4.7/build/seed.js');
    }
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/helper.js?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/pages_class.js?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/uploadzone/modules/top_part/js/default.js?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/uploadzone/modules/file_list/js/default.js?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/ks/modules/file_upload/upload_zone.js?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/kindeditor.js');
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/lang/zh_CN.js');
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/plugins/code/prettify.js');

    if ('window' == App\Classes\Utils\ComponentReactJS::$env) {
//        \App\Classes\Utils\FrontBuilder::pushJs(config('common.public_ice_dist_url') . 'public/js/react-bundle.min.js');
        \App\Classes\Utils\FrontBuilder::pushJs(config('common.public_ice_dist_url') . 'build/library/js/block.js?v=' . $front_version);
    }
    echo \App\Classes\Utils\FrontBuilder::showAllJs();
    ?>
    <script>
        <?php echo $js_all; ?>
        <?php
        if ($module_id_mapping_key) {
            foreach ($module_id_mapping_key as $module_id => $key) {
                echo '$("#' . $module_id . '").html(rendorBox["' . $key . '"]);';
            }
        }
        ?>
    </script>
    <script>
        webConfig.module_debug = true;
        webConfig.module_tag = '';
        webConfig.isFromShop = true;
        webConfig.previewUser = 123;
        webConfig.is_edit_one_module = true;
        webConfig.app_name = '<?php echo $app_name?>';
        webConfig.module_type_name = 'react';
        var PUBLIC_URL = "<?php echo $front_public_domain; ?>laravle-amodvis/amodvis/";
        var FRONT_DOMAIN = "<?php echo $front_public_domain; ?>";
        var API_URL = "<?php echo config('common.amod_api_cms_domain'); ?>api/";
        var ADMIN_URL = "<?php echo config('common.amod_api_cms_domain'); ?>api/";
        var UPLOAD_URL = "<?php echo config('common.amod_api_cms_domain'); ?>api/";
        var JS_DOMAIN = "<?php echo config('common.amod_js_domain');?>";
        var APP_DOMAIN = "<?php echo $app_domain;?>";
        var srcPath = PUBLIC_URL + "js/ks";
        var moduleBuildPath = PUBLIC_URL + "js/ks/build/modules";
        KISSY.config({
            combine: !!parseInt("<?php echo $combine ? 1 : 0;?>"),
            packages: {
                "kg/calendar/2.0.2/index": {
                    base: srcPath + '/gallery/calendar/2.0.2/build/index',
                    tag: '<?php echo $front_version;?>',
                    ignorePackageNameInUri: true
                },
                "kg/datetimepicker/2.0.0/index": {
                    base: srcPath + '/gallery/datetimepicker/2.0.0/build/index',
                    tag: '<?php echo $front_version;?>',
                    ignorePackageNameInUri: true
                },
                "modules": {
                    base: moduleBuildPath,
                    charset: "utf-8",
                    tag: "<?php echo $front_version;?>",
                    combine: false,
                    ignorePackageNameInUri: true
                }
            }
        });
        <?php
        if (true === $admin) {
        ?>
        KISSY.use("modules/module_init/module_edit", function (S, ModuleEdit) {
            ModuleEdit.init();
        });
        KISSY.use("modules/module_init/page_edit", function (S, PageEdit) {
            PageEdit.init();
        });
        KISSY.use("modules/module_init/init_widget");
        KISSY.use("modules/module_components/calendar");
        KISSY.use("modules/module_components/datetimepicker");
        <?php
        }
        ?>
    </script>
    <?php
    }
    ?>
    <?php echo $app_info->foot_content ?? ''; ?>
    <?php echo $app_page_info->foot_content ?? ''; ?>
@endsection
