<?php

use Illuminate\Routing\Router;

Route::group([
    'middleware' => 'dev',
], function (Router $router) {
    $router->post("dev/kit/sql", 'KitController@sql');
});
