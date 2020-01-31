<?php

namespace App\Classes\Models\Amod;

use Illuminate\Support\Facades\DB;

class ModuleDataDao
{
    public function save($module_row_id, $post_data)
    {
        $json_data = json_encode($post_data);
        $sql = 'insert into `amod_module_data` (`module_row_id`,`json_data`) value(
        :module_row_id,
        :json_data
        ) on duplicate key update `json_data`=:update_json_data';
        return DB::insert($sql, [$module_row_id, $json_data, $json_data]);
    }

    public function getOne($module_row_id)
    {
        $model = DB::table('amod_module_data');
        $model->where('module_row_id', '=', $module_row_id);
        return $model->first();
    }

    public function getListByModuleRowIdArr($module_row_id_arr)
    {
        $model = DB::table('amod_module_data');
        $model->whereIn('module_row_id', $module_row_id_arr);
        $ret = $model->get()->toArray();
        $res_mapping = [];
        foreach ($ret as $row) {
            $res_mapping[$row->module_row_id] = $row;
        }
        return $res_mapping;
    }
}
