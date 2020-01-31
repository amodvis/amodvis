<?php

namespace Amvphp\uploadzone\file_list;

use App\Classes\Widget\Widget;
use App\Classes\Services\Media\MediaService;

class DefaultWidget extends Widget
{
    public function run($view)
    {
        $user_id = request()->input('login_vendor_id');
        $node_id = request()->input('node_id');
        $page = request()->input('page', 1);
        $view['category_list'] = app(MediaService::class)->getNodesByParentId($user_id, $node_id);
        $page_options = [
            'page' => $page,
            'page_size' => 30,
        ];
        $view['items'] = app(MediaService::class)->getItemsByPathId($user_id, $node_id, $page_options);
        return $this->view(__DIR__ . '/views', 'index', $view);
    }
}