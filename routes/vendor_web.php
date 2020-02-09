<?php
/**
 * 商家后台WEB
 */
$letter_num = '[A-Za-z]+[A-Za-z0-9_]*';
$page_name_verify = '[A-Za-z]+[A-Za-z0-9_\-:]*';
$module_unique = ['app_name' => $letter_num, 'project_name' => $letter_num, 'module_name' => $letter_num,
    'page_name' => $page_name_verify, 'position' => '[0-9]+'];
$module_unique_path = '{app_name}/{project_name}/{module_name}/{page_name}/{position}';
Route::group(['middleware' => ['vendor_web_force_auth']], function () use (
    $module_unique, $module_unique_path,
    $letter_num, $page_name_verify
) {
    Route::get('/module_api/design/get_product_edit/' . $module_unique_path . '/{product_id}/{trigger_name}/{module_type_name}',
        'CoreModule\IndexController@getProductModuleEditHtml')->where(
        array_merge($module_unique, ['product_id' => '[0-9]+', 'trigger_name' => $letter_num, 'module_type_name' => $letter_num])
    );
    Route::get('/get_item/' . $module_unique_path, 'Module\ReactIndexController@getItem')->where(
        array_merge($module_unique, [])
    );
    Route::get('/page_builder/{app_name}/{page_name}', 'Module\ReactIndexController@pageBuilder')->where(
        ['app_name' => $letter_num, 'page_name' => $page_name_verify]
    );
    Route::get('/page_builder_mobile/{app_name}/{page_name}', 'Module\ReactIndexController@pageBuilderMobile')->where(
        ['app_name' => $letter_num, 'page_name' => $page_name_verify]
    );
    Route::get('/page_mobile_view/{app_name}/{page_name}', 'Module\ReactIndexController@pageBuilderMobileView')->where(
        ['app_name' => $letter_num, 'page_name' => $page_name_verify]
    );
    Route::get('/a_get_item/' . $module_unique_path, 'Module\CommonIndexController@getItem')->where(
        array_merge($module_unique, [])
    );
    Route::get('/a_page_builder/{app_name}/{page_name}', 'Module\CommonIndexController@pageBuilder')->where(
        ['app_name' => $letter_num, 'page_name' => $page_name_verify]
    );
    Route::get('/app_project_list/{app_name}', 'IndexController@appModuleList')->where(
        ['project_name' => $letter_num, 'module_name' => $letter_num]
    );
    Route::get('/modules_info/{app_name}/{project_name}/{module_name?}', 'IndexController@appProjectModuleList')->where(
        ['app_name' => $letter_num, 'project_name' => $letter_num, 'module_name' => $letter_num]
    );
    Route::get('/pages_info/{project_name}', 'IndexController@createAppPage')->where(
        ['project_name' => $letter_num]
    );
    Route::get('/a_modules_info/{project_name}/{module_name?}', 'Admin\IndexController@appModuleList')->where(
        ['project_name' => $letter_num, 'module_name' => $letter_num]
    );
    Route::get('/a_pages_info/{project_name}', 'Module\IndexController@appPageList')->where(
        ['project_name' => $letter_num]
    );
    Route::get('/media_library', 'IndexController@mediaIndex');
    Route::get('/example_app', 'IndexController@exampleApp');
    Route::get('/create_app', 'IndexController@createApp');
    Route::get('/', 'IndexController@createApp');
});
