<?php

namespace App\Classes\Models\Media;

use Illuminate\Support\Facades\DB;

class  FilePathDao
{
    public function addOne($user_id, $path_name)
    {
        $model = DB::table('media_file_path');
        $data['user_id'] = $user_id;
        $data['path_name'] = $path_name;
        return $model->insertGetId($data);
    }

    public function renameFile($user_id, $id, $nick_name)
    {
        $model = DB::table('media_file_item');
        $model->where('id', '=', $id)
            ->where('user_id', '=', $user_id);
        $data['nick_name'] = $nick_name;
        return $model->update($data);
    }

    public function delFile($user_id, $id)
    {
        $model = DB::table('media_file_item');
        $model->where('id', '=', $id)
            ->where('user_id', '=', $user_id);
        return $model->delete();
    }

    public function getByIds($path_ids)
    {
        $model = DB::table('media_file_path');
        $model->whereIn('id', $path_ids);
        return $model->get()->toArray();
    }

    public function getNodesByParentId($user_id, $parent_id, $page_options, $order_options = [])
    {
        $model = DB::table('media_file_path');
        $page = $page_options['page'];
        $page_size = $page_options['page_size'];
        $obj = $model->where('user_id', '=', $user_id)
            ->where('parent_id', $parent_id);
        $count = $obj->count();
        $obj->forPage($page, $page_size);
        foreach ($order_options as $order_row) {
            $obj->orderBy($order_row[0], $order_row[1]);
        }
        $items = $obj->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }
}
