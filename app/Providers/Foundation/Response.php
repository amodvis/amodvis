<?php

namespace App\Providers\Foundation;

use App\Providers\DeferredSingletonProvider;

use ES\Response\CommonResponse;

class Response extends DeferredSingletonProvider
{
    protected $deferSingletons = [
        'app.response' => CommonResponse::class,
    ];
}
