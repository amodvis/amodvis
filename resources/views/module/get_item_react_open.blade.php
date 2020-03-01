<?php
$front_version = getFrontVersion();
$api_data = '[
    {
        "path": "/index",
        "is_pull_update": true,
        "modules": {
            "hd": [],
            "bd": [
                 {
                    "main": [
                        {
                            "project_name": "' . $json_item['project_name'] . '",
                            "module_name": "' . $json_item['module_name'] . '",
                            "page_name": "' . $json_item['page_name'] . '",
                            "position": "' . $json_item['position'] . '",
                            "module_data": ' . json_encode($json_item["module_data"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '
                        }
                    ]
                }
            ],
            "ft": []
        }
    }
]';
?>

window.isMobile = <?php echo isMobile() ? 'true' : 'false';?>;
window.debuglog = function(info){
console.log(info);
}
window.pageApiData = <?php echo $api_data;?>;
window.app_name_by_domain = '<?php echo $app_name;?>';
window.app_key = '';
window.shop_vendor_token = '<?php echo $shop_vendor_token;?>';
window.moduleFetchUrl = '<?php echo config('common.module_fetch_url');?>';
window.publicIceDistUrl = '<?php echo config('common.public_ice_dist_url');?>';
window.userSSOUrl = '<?php echo config('common.user_sso_url');?>';

window.onload = function () {

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
addScript("<?php echo config('common.public_ice_dist_url') . 'build/js/index.js?v=' . $front_version;?>", false);
addStyle("<?php echo config('common.public_ice_dist_url') . 'build/css/index.css?v=' . $front_version;?>", false);
