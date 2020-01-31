<?php

namespace App\Classes\Models\Media;

use Illuminate\Support\Facades\DB;

class  FileItemDao
{
    public function getCountByNode($user_id, $node_id)
    {
        $model = DB::table('media_file_item');
        $model->where('user_id', '=', $user_id);
        if ($node_id) {
            $model->where('path_id', $node_id);
        }
        return $model->count();
    }

    public function addOne($user_id, $data)
    {
        $model = DB::table('media_file_item');
        $data['user_id'] = $user_id;
        $model->insert($data);
    }

    public function getList($user_id, $path_id, $page_options, $order_options = [])
    {
        $model = DB::table('media_file_item');
        $page = $page_options['page'];
        $page_size = $page_options['page_size'];
        $model->where('user_id', '=', $user_id);
        if ($path_id) {
            $model->where('path_id', $path_id);
        }
        $count = $model->count();
        $model->forPage($page, $page_size);
        foreach ($order_options as $order_row) {
            $model->orderBy($order_row[0], $order_row[1]);
        }
        $items = $model->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }
}