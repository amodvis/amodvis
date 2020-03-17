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
        <div style="min-height:auto;" class="J_TModule J_TEmptyBox J_ModuleOneSetup" data_module_no_reload=1
             data-dir="<?php echo $project_name; ?>" data-page="" moduleid="" data-position="" data_page_reload=1>
            <a class="ds-bar-edit" href="javascript:void(0);"><span>&nbsp;&nbsp;</span></a>
        </div>
        <div class="module_one_setup_items">
            <ul>
                <li class="form-default" style="display: <?php if ($module_name) {
                    echo 'none';
                }?>"><span>模块</span><select class="input-box J_AddModuleName">
                        <?php
                        $i = 1;
                        foreach ($module_name_list as $item) {
                            $select = '';
                            if (1 === $i) {
                                $select = 'selected="selected"';
                            }
                            echo '<option value="' . $item->module_name . '" ' . $select . '>' . ($item->nick_name ?: $item->module_name) . '</option>';
                            $i++;
                        }
                        ?>
                    </select>
                </li>
                <li><span>页面</span><input type="text" class="J_AddPageName"/></li>
                <li><span>活动位置ID</span><input type="text" class="J_AddPosition"/></li>
            </ul>
            <span class="J_UpsetOneModule setup_btn">新增</span>
        </div>
    </div>
    <?php
    echo '<table class="fashiontable">';
    echo '<tr><th width="15%">project_name</th><th width="20%">module_name</th><th width="25%">page_name</th><th width="10%">position</th><th width="30%">操作</th></tr>';
    foreach ($page_list['data'] as $row) {

        $app_domain = $app_info->app_domain ? request()->getScheme() . '://' . $app_info->app_domain : $front_base_url;

        $setting_html = '
<div class="table_list_btns">
<span style="min-height:auto;" class="J_TModule J_TEmptyBox" data_module_no_reload=1 data-dir="' . $row->project_name_v . '" data-page="' . $row->page_name . '"
moduleid="' . $row->module_name . '" data-position="' . $row->position . '" data_height="420">
<a class="ds-bar-edit" href="javascript:void(0);"><span>设置</span></a>
</span>
<a target="_blank" href="/get_item/' . $app_name . '/' . $row->project_name_v . '/' . $row->module_name . '/' . $row->page_name . '/' . $row->position . '?admin=1&design=1">可视化</a>
<a target="_blank" href="' . $app_domain . '/module_api/get_item_open/' . $app_name . '/' . $row->project_name_v . '/' . $row->module_name . '/' . $row->page_name . '/' . $row->position . '.js">复制URL引入JS</a>
<a target="_blank" href="' . getOriginEnv('AMOD_API_BASE_URL') . 'api/module_api/index/get_one_module_data/' . $app_name . '/' . $row->project_name_v . '/' . $row->module_name . '/' . $row->page_name . '/' . $row->position . '">获取基础API</a>
<a target="_blank" href="' . getOriginEnv('AMOD_API_BASE_URL') . 'api/module_api/index/get_one_module_product_by_page/' . $app_name . '/' . $row->project_name_v . '/' . $row->module_name . '/' . $row->page_name . '/' . $row->position . '">获取商品翻页API</a>
</div></div>
';
        echo '<tr><td>' . $row->project_name_v . '</td><td>' . $row->module_name . '</td>
        <td>' . $row->page_name . '</td><td>' . $row->position . '</td><td>' . $setting_html . '</td></tr>';
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
    <script>
        $.ajax({
            type: "GET",
            url: "<?php echo getOriginEnv('AMOD_API_BASE_URL');?>api/app_backend/vendor_bind/login/sso?shop_vendor_token=<?php echo $shop_vendor_token;?>",
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
@endsection
@section('css')
    <?php
    echo \App\Classes\Utils\FrontBuilder::showAllCss();
    ?>
@endsection
