<?php

use  \App\Classes\Utils\UtilsCommon;

ob_start();
$front_public_domain = config('common.amod_front_public_domain');
$front_version = getFrontVersion();
$module_unique_id = 1001;
$module_id = 'shopModuleId' . $module_unique_id;
if (isset($_GET['module_item_id']) && preg_match('/[A-Za-z]+[A-Za-z0-9_\-:]*/', $_GET['module_item_id'])) {
    $module_id = $_GET['module_item_id'];
}
$rjs = new \App\Classes\Utils\ComponentReactJS();
$js_all = $rjs->init_js;
$ret = UtilsCommon::getReactModuleHtml($rjs, $json_item, $page_location, 'shopModuleId' . $module_unique_id);
if (false === $ret['error']) {
    $js_all .= $ret['data'];
}
$logic_html = ob_get_contents();
ob_end_clean();
$logic_js = '';
$module_type_name = 'react';
UtilsCommon::modulePageView($design, $admin, $app_name, $module_type_name, $logic_html, $logic_js);
?>
<script type="text/javascript">
    window.onload = function () {
        <?php
        echo $js_all;
        $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
        echo 'document.getElementById("' . $module_id . '").innerHTML=rendorBox["' . $key . '"];';
        ?>
    };

    function addScript(url, inline) {
        var head = document.getElementsByTagName("head")[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        if (inline) {
            script.text = url;
        } else {
            script.src = url;
        }
        head.appendChild(script);
    }

    function addStyle(url, inline) {
        var head = document.getElementsByTagName("head")[0];
        var script = document.createElement('link');
        script.type = 'text/css';
        script.rel = "stylesheet";
        if (inline) {
            script.text = url;
        } else {
            script.href = url;
        }
        head.appendChild(script);
    }

    addScript("<?php echo config('common.public_ice_dist_url') . 'build/library/js/block.js?v=' . $front_version;?>", false);
    addStyle("<?php echo config('common.public_ice_dist_url') . 'build/css/index.css?v=' . $front_version;?>", false);
</script>
