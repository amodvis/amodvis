<?php

namespace App\Classes\Models\Amod;

use Illuminate\Support\Facades\DB;

class ModuleBasicDao
{
    public function getList($project_name, $page_options)
    {
        $model = DB::table('amod_module_basic');
        $where = [
            ['project_name', '=', $project_name],
        ];
        array_map(function ($v) use ($model) {
            call_user_func_array([$model, 'where'], $v);
        }, $where);
        $count = $model->count();
        $obj = $model->forPage($page_options['page'], $page_options['page_size']);
        $items = $obj->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }

    public function getModuleInfoByCache($project_name, $module_name)
    {
        $model = DB::table('amod_module_basic');
        $model->where('project_name', '=', $project_name);
        $model->where('module_name', '=', $module_name);
        return $model->first();
    }

    public function getListByModuleNames($project_name, $module_names)
    {
        $model = DB::table('amod_module_basic');
        $model->where('project_name', '=', $project_name);
        $model->whereIn('module_name',  $module_names);
        return $model->get()->toArray();
    }
}
