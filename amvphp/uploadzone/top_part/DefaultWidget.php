<?php

namespace Amvphp\uploadzone\top_part;

use App\Classes\Widget\Widget;
use App\Classes\Services\Media\MediaService;

class DefaultWidget extends Widget
{
    public function run($view)
    {
        $user_id = request()->input('login_vendor_id');
        $node_id = request()->input('node_id') ?: 0;
        $view['category_list'] = app(MediaService::class)->getNodesByParentId($user_id, $node_id);
        $view['item_count'] = app(MediaService::class)->getItemCountByNode($user_id, $node_id);
        if ($node_id) {
            $view['all_count'] = app(MediaService::class)->getItemCountByNode($user_id, 0);
        } else {
            $view['all_count'] = $view['item_count'];
        }
        $view['category_list'] = $view['category_list']['data'] ?? [];
        return $this->view(__DIR__ . '/views', 'index', $view);
    }
}