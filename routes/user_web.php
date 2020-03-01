<?php
$letter_num = '[A-Za-z]+[A-Za-z0-9_]*';
$page_name_verify = '[A-Za-z]+[A-Za-z0-9_\-:]*';
$module_unique = ['app_name' => $letter_num, 'project_name' => $letter_num, 'module_name' => $letter_num, 'page_name' => $page_name_verify, 'position' => '[0-9]+'];
$module_unique_path = '{app_name}/{project_name}/{module_name}/{page_name}/{position}';
/**
 * 前台WEB
 */
Route::group(['middleware' => ['only_filter_and_options']], function () {
    Route::any('/app_front/vendor_bind/login/sso', 'LoginController@bindShopVendor');
    Route::any('/user/login/sso', 'LoginController@userLogin');
});
Route::group(['middleware' => ['user_web_not_force_auth']], function () use ($module_unique, $module_unique_path) {
    Route::get('/module_api/get_item_open/' . $module_unique_path . '.js', 'ReactIndexController@getItem')->where(
        array_merge($module_unique, [])
    );
    Route::get('/', 'ReactIndexController@frontDefault')->middleware('cache.headers:public;etag;max_age=300;');
    Route::get('/index', 'ReactIndexController@frontDefault')->middleware('cache.headers:public;etag;max_age=300;');
    Route::get('/article/{item_id}', 'ReactIndexController@frontArticleDetail')->where(
        ['product_id' => '[0-9]+']
    );
    Route::get('/product/{item_id}', 'ReactIndexController@frontProductDetail')->where(
        ['article_id' => '[0-9]+']
    )->middleware('cache.headers:public;etag;max_age=300;');
    Route::get('/item_search/{keyword}', 'ReactIndexController@frontItemSearch')->where(
        ['keyword' => '[^/]+']
    );
    Route::get('/order/confirm/{content}', 'ReactIndexController@orderConfirm')->where(
        ['content' => '[^/]+']
    );
    Route::get('/{page_name}', 'ReactIndexController@frontIndex')->where(
        ['page_name' => '[a-zA-Z0-9_/]+']
    );
});
