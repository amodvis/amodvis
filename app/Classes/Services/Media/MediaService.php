<?php

namespace App\Classes\Services\Media;

use App\Classes\Models\Media\FilePathDao;
use App\Classes\Models\Media\FileItemDao;

class MediaService
{
    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_VIDEO = 2;
    const FILE_TYPE_AUDIO = 3;
    const FILE_TYPE_OTHER = 4;

    public function getPathByIds($path_ids)
    {
        return app(FilePathDao::class)->getByIds($path_ids);
    }

    public function addOneFile($user_id, $data)
    {
        return app(FileItemDao::class)->addOne($user_id, $data);
    }

    public function addOnePath($user_id, $path_name)
    {
        return app(FilePathDao::class)->addOne($user_id, $path_name);
    }

    public function renameFile($user_id, $id, $nick_name)
    {
        return app(FilePathDao::class)->renameFile($user_id, $id, $nick_name);
    }

    public function delFile($user_id, $id)
    {
        return app(FilePathDao::class)->delFile($user_id, $id);
    }

    public function getItemsByPathId($user_id, $path_id, $page_options)
    {
        $order_options = [
            ['create_time', 'desc']
        ];
        $items = app(FileItemDao::class)->getList($user_id, $path_id, $page_options, $order_options);
        $path_id_arr = [];
        $items_data = $items['data'] ?? [];
        foreach ($items_data as $item) {
            $path_id_arr[$item->path_id] = $item->path_id;
        }
        $path_id_arr = array_values($path_id_arr);
        $ret = app(MediaService::class)->getPathByIds($path_id_arr);
        $path_name_mapping = [];
        foreach ($ret as $row) {
            $path_name_mapping[$row->id] = $row->path_name;
        }
        foreach ($items['data'] as $item) {
            $item->path_name = $path_name_mapping[$item->path_id] ?? '';
            $item->file_basic_name = substr($item->file_name, strlen(dirname($item->file_name)) + 1);
        }
        return $items;
    }

    public function getNodesByParentId($user_id, $parent_id)
    {
        $page_options = ['page' => 1, 'page_size' => 50];
        $order_options = [
            ['create_time', 'desc']
        ];
        return app(FilePathDao::class)->getNodesByParentId($user_id, $parent_id, $page_options, $order_options);
    }

    public function getItemCountByNode($user_id, $node_id)
    {
        return app(FileItemDao::class)->getCountByNode($user_id, $node_id);
    }
}