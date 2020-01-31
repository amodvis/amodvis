@extends('layouts.app')
<?php

use App\Classes\Utils\UtilsCommon;
$combine = getOriginEnv('KISSY_BASIC_COMBINE') ? 1 : 0;
$front_version = getFrontVersion();
list($content, $project_arr) = UtilsCommon::getCommonPageModulesHtml($login_vendor_id, $app_name,
    $page_modules_html_options);
?>
@section('title')
    <?php echo $title ?? '';?>
@endsection
@section('css')
    <?php
    $front_public_domain = config('common.amod_front_public_domain');
    if (true === $design) {
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/design.css?v=' . $front_version);
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/debug.css?v=' . $front_version);
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/common.css?v=' . $front_version);
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/module_components.css?v=' . $front_version);
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/uploadzone/public/stylesheet/default.css?v=' . $front_version);
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/uploadzone/modules/top_part/stylesheet/default.css?v=' . $front_version);
        \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/uploadzone/modules/file_list/stylesheet/default.css?v=' . $front_version);
    }
    echo \App\Classes\Utils\FrontBuilder::showAllCss();
    ?>
@endsection
@section('content')
    <?php
    echo '<div id="page">';
    echo $content;
    echo '</div>';
    if (true === $design) {
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
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/ks/modules/file_upload/upload_zone.js?v=' . $front_version);
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/kindeditor.js');
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/lang/zh_CN.js');
    \App\Classes\Utils\FrontBuilder::pushJs($front_public_domain . 'laravle-amodvis/amodvis/js/kindeditor-4.1.10/plugins/code/prettify.js');
    echo \App\Classes\Utils\FrontBuilder::showAllJs();
    foreach ($project_arr as $item) {
        echo '<script>
        JuDianCommon_' . $item['project_name'] . '.' . $item['module_name'] . '.init();
    </script >';
    }
    ?>
    <script>
        if (typeof webConfig === "undefined") {
            window.webConfig = {};
        }
        webConfig.module_debug = true;
        webConfig.module_tag = '';
        webConfig.isFromShop = true;
        webConfig.previewUser = '';
        webConfig.is_edit_one_module = true;
        webConfig.app_name = '<?php echo $app_name?>';
        webConfig.module_type_name = 'common';
        var PUBLIC_URL = "<?php echo $front_public_domain; ?>laravle-amodvis/amodvis/";
        var FRONT_DOMAIN = "<?php echo $front_public_domain; ?>";
        var API_URL = "<?php echo config('common.amod_api_cms_domain');?>api/";
        var ADMIN_URL = "<?php echo config('common.amod_api_cms_domain');?>api/";
        var UPLOAD_URL = "<?php echo config('common.amod_api_cms_domain');?>api/";
        var JS_DOMAIN = "<?php echo config('common.amod_js_domain');?>";
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
                    tag: '<?php echo $front_version;?>',
                    combine: false,
                    ignorePackageNameInUri: true
                }
            }
        });
        <?php
        if (isset($kissy_use)) {
            echo $kissy_use;
        }
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
@endsection
