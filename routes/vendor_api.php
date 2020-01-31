<?php
/**
 * 商家后台API
 */
$letter_num = '[A-Za-z]+[A-Za-z0-9_]*';
$page_name_verify = '[A-Za-z]+[A-Za-z0-9_\-:]*';
$module_unique = ['app_name' => $letter_num, 'project_name' => $letter_num, 'module_name' => $letter_num,
    'page_name' => $page_name_verify, 'position' => '[0-9]+'];
$module_unique_path = '{app_name}/{project_name}/{module_name}/{page_name}/{position}';

Route::group(['middleware' => ['vendor_api_not_force_auth']], function () use ($module_unique, $module_unique_path) {
    Route::any('/vendor/login/sso', 'LoginController@login');
});
Route::group(['middleware' => ['vendor_api_force_auth']], function () use (
    $module_unique, $module_unique_path,
    $letter_num, $page_name_verify
) {
    Route::get('/admin_api/get_shop_by_vendor/{vendor_id}', 'BaseController@getShopByVendorId')->where(
        ['vendor_id' => '[0-9]+']
    );
    // 商品组件相关 start
    Route::post('/module_api/components/ordermoduleitem', 'CustomizedComponent\ProductComponentController@orderModuleProduct');
    Route::get('/module_api/components/itemquery/' . $module_unique_path, 'CustomizedComponent\ProductComponentController@itemQuery')->where(
        array_merge($module_unique, [])
    );
    Route::post('/module_api/components/chooseremove/' . $module_unique_path, 'CustomizedComponent\ProductComponentController@chooseRemove')->where(
        array_merge($module_unique, [])
    );
    Route::post('/module_api/design/save_one_module_product/' . $module_unique_path . '/{product_id}/{trigger_name}', 'CustomizedComponent\ProductComponentController@saveOneModuleProduct')->where(
        array_merge($module_unique, ['product_id' => '[0-9]+', 'trigger_name' => $letter_num])
    );
    // 商品组件相关 end

    // 模块核心功能 start
    Route::get('/module_api/design/getmoduleedit/' . $module_unique_path . '/{module_type_name}', 'CoreModule\IndexController@getModuleEditHtml')->where(
        array_merge($module_unique, ['module_type_name' => $letter_num])
    );
    Route::post('/module_api/design/savemodule/' . $module_unique_path, 'CoreModule\IndexController@saveModule')->where(
        array_merge($module_unique, [])
    );
    Route::get('/module_web/modules/getmodulehtml/' . $module_unique_path . '/{module_type_name}/{module_unique_id}', 'CoreModule\IndexController@getModuleHtml')->where(
        array_merge($module_unique, ['module_type_name' => $letter_num, 'module_unique_id' => $letter_num])
    );
    Route::get('/moduleapi/design/getmoduleslist/{project_name}', 'CoreModule\IndexController@getModulesList')->where(
        ['project_name' => $letter_num]
    );
    // 模块核心功能 end

    // 文件素材库相关 start
    Route::post('/module_api/upload/uploadkissy', 'UploadController@uploadkissy');
    Route::get('/module_api/media/file_list', 'FileLibraryController@getUploadZoneFileList');
    Route::post('/module_api/media/add_one_file', 'FileLibraryController@addOneFile');
    Route::post('/module_api/media/add_one_path', 'FileLibraryController@addOnePath');
    Route::post('/module_api/media/rename_file', 'FileLibraryController@renameFile');
    Route::post('/module_api/media/del_file', 'FileLibraryController@delFile');
    // 文件素材库相关 end

    // 自定义组件HTML获取 start
    Route::get('/module_web/modules/item_choose', 'CustomizedComponent\HtmlController@itemChoose');
    Route::get('/module_web/media/moduleindex', 'CustomizedComponent\HtmlController@mediaSelector');
    Route::get('/module_web/modules/html_editor', 'CustomizedComponent\HtmlController@htmlEditor');
    Route::get('/module_web/modules/module_choose', 'CustomizedComponent\HtmlController@moduleChoose');
    // 自定义组件HTML获取 end

    Route::get('/module_api/create_app/get_html', 'CommonModuleXml\Basic\AppController@getHtml');
    Route::get('/module_api/create_app_page/get_html/{app_name}', 'CommonModuleXml\Basic\AppPageController@getHtml')->where(
        ['app_name' => $letter_num]
    );
    Route::get('/module_api/create_app_page/get_layout_html/{app_name}/{page_name}', 'CommonModuleXml\Basic\AppPageController@getLayoutHtml')->where(
        ['app_name' => $letter_num],
        ['page_name' => $page_name_verify]
    );
    Route::post('/module_api/create_app/upset', 'CommonModuleXml\Basic\AppController@upset');
    Route::post('/module_api/create_app_page/upset/{app_name}', 'CommonModuleXml\Basic\AppPageController@upset')->where(
        ['app_name' => $letter_num]
    );
    Route::post('/module_api/create_app_page/update_layout/{app_name}/{page_name}', 'CommonModuleXml\Basic\AppPageController@updateLayout')->where(
        ['app_name' => $letter_num],
        ['page_name' => $page_name_verify]
    );
});
