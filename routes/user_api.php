<?php
/**
 * 前台WEB
 */
$letter_num = '[A-Za-z]+[A-Za-z0-9_]*';
$page_name_verify = '[A-Za-z]+[A-Za-z0-9_\-:]*';
$module_unique = ['app_name' => $letter_num, 'project_name' => $letter_num, 'module_name' => $letter_num, 'page_name' => $page_name_verify, 'position' => '[0-9]+'];
$module_unique_path = '{app_name}/{project_name}/{module_name}/{page_name}/{position}';

Route::group(['middleware' => ['only_filter_and_options']], function () use ($module_unique_path, $module_unique) {
    Route::any('/app_backend/vendor_bind/login/sso', 'LoginController@bindShopVendor');
    Route::any('/user/login/sso', 'LoginController@userLogin');
});
Route::group(['middleware' => ['user_api_not_force_auth']], function () use ($module_unique, $module_unique_path, $letter_num) {
    Route::get('/tool/get_app_router_config', 'ToolsController@getRouterConfig');
    Route::any('/module_api/index/get_modules_data/{app_name?}', 'CoreModule\IndexController@getModulesData')->where(
        ['app_name' => $letter_num]
    )->middleware('cache.headers:public;etag;max_age=300;');
    Route::get('/module_api/index/get_one_module_data/' . $module_unique_path, 'CoreModule\IndexController@getOneModuleData')->where(
        array_merge($module_unique, [])
    );
    Route::any('/module_api/index/get_one_module_product_by_page/' . $module_unique_path, 'CoreModule\IndexController@getOneModuleProductByPage')->where(
        array_merge($module_unique, [])
    );
    Route::any('/module_api/components/product_list/' . $module_unique_path, 'CoreModule\IndexController@productList')->where(
        array_merge($module_unique, [])
    );
});
