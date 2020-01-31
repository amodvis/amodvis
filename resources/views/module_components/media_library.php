<?php
$content = '';
$project_arr = [];

use App\Classes\Utils\UtilsCommon;

list($content, $project_arr) = UtilsCommon::getCommonPageModulesHtml($login_vendor_id, $app_name,
    $page_modules_html_options);
echo $content;
foreach ($project_arr as $item) {
    echo '<script>
            JuDianCommon_' . $item['project_name'] . '.' . $item['module_name'] . '.init();
        </script >';
}
?>
