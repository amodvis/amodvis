<?php
/**
 * 前台WEB
 */
Route::group(['middleware' => ['only_filter_and_options']], function () {
    Route::any('/app_front/vendor_bind/login/sso', 'LoginController@bindShopVendor');
    Route::any('/user/login/sso', 'LoginController@userLogin');
});
Route::group(['middleware' => ['user_web_not_force_auth']], function () {
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
