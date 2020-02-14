<!doctype html>
<html>
<head>
    <title><?php echo $app_info->app_name_cn ?: $app_info->app_name; ?></title>
    <meta name="viewport"
          content="width=device-width,user-scalable=no,minimum-scale=1,maximum-scale=1,shrink-to-fit=no,">
    <?php $front_public_domain = config('common.amod_front_public_domain'); ?>
    <!-- css and stuff -->
    <link rel="shortcut icon" href="<?php echo $front_public_domain; ?>laravle-amodvis/ice_dist/build/favicon.png">
    <link
        href="<?php echo config('common.public_ice_dist_url') ?>build/css/index.css?v=<?php echo getFrontVersion(); ?>"
        rel="stylesheet">
    <?php
    $page_location = $page_name;
    $js_content = '
    global.pageLocation = "' . $page_location . '";
    global.pageApiData = ' . $page_api_data . ';
    ';
    ?>
    <script>
        window.isMobile = <?php echo isMobile() ? 'true' : 'false';?>;
        window.debuglog = function(info){
            console.log(info);
        }
        window.pageApiData = <?php echo $page_api_data;?>;
        window.app_name_by_domain = '<?php echo $app_name_by_domain;?>';
        window.app_key = '<?php echo $app_info->app_key;?>';
        window.shop_vendor_token = '<?php echo $shop_vendor_token;?>';
        window.moduleFetchUrl = '<?php echo config('common.module_fetch_url');?>';
        window.publicIceDistUrl = '<?php echo config('common.public_ice_dist_url');?>';
        window.userSSOUrl = '<?php echo config('common.user_sso_url');?>';
    </script>
    <?php echo $app_info->head_content ?? ''; ?>
    <?php echo $app_page_info->head_content ?? ''; ?>
    <?php if (getOriginEnv('DEBUG_ROUTER')) {
        $front_public_domain = config('common.amod_front_public_domain')
        ?>
        <script src="<?php echo $front_public_domain; ?>laravle-amodvis/amodvis/js/jquery-1.8.1.min.js"></script>
    <?php } ?>
</head>
<body>
<!-- render server content here -->
<div id="ice-container"><?php
    if (class_exists('\V8Js')) {
        $env = getOriginEnv('REACT_RUN_ENV') ?? 'v8';
    } else {
        $env = 'window';
    }
    if ('v8' === $env) {
        $v8 = new \App\Classes\Utils\PageReactJS();
        echo $v8->executeJS($js_content .
            file_get_contents(config('common.public_ice_dist_url') . 'build/js/server.js')
        );
    }
    ?></div>
<script type="text/javascript"
        src="<?php echo config('common.public_ice_dist_url'); ?>build/js/index.js?v=<?php echo getFrontVersion(); ?>"></script>
<?php echo $app_info->foot_content ?? ''; ?>
<?php echo $app_page_info->foot_content ?? ''; ?>
<?php
echo App\Widget\amodvis_admin\version_control\DefaultWidget::widget();
?>
</body>
</html>
