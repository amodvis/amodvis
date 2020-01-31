<?php

namespace App\Classes\Models\Amod;

use Illuminate\Support\Facades\DB;

class ModuleUniqueDao
{
    public function getRowId($user_id, $app_name, $module_options)
    {
        $model = DB::table('amod_module_unique');
        $where = [
            ['user_id', '=', $user_id],
            ['app_name', '=', $app_name],
        ];
        $insert['user_id'] = $user_id;
        $insert['app_name'] = $app_name;
        if (!empty($module_options['project_name'])) {
            $where[] = ['project_name_v', '=', $module_options['project_name']];
            $insert['project_name_v'] = $module_options['project_name'];
        }
        if (!empty($module_options['module_name'])) {
            $where[] = ['module_name', '=', $module_options['module_name']];
            $insert['module_name'] = $module_options['module_name'];
        }
        if (!empty($module_options['page_name'])) {
            $where[] = ['page_name', '=', $module_options['page_name']];
            $insert['page_name'] = $module_options['page_name'];
        }
        if (!empty($module_options['position'])) {
            $where[] = ['position', '=', $module_options['position']];
            $insert['position'] = $module_options['position'];
        }
        if (!empty($module_options['module_tag'])) {
            $where[] = ['module_tag', '=', $module_options['module_tag']];
            $insert['module_tag'] = $module_options['module_tag'];
        }
        array_map(function ($v) use ($model) {
            call_user_func_array([$model, 'where'], $v);
        }, $where);
        $ret = $model->first();
        if (empty($ret)) {
            return $model->insertGetId($insert);
        }
        return empty($ret) ? 0 : $ret->id;
    }

    public function getListByModule($user_id, $app_name, $module_options, $page_options)
    {
        $model = DB::table('amod_module_unique');
        $where = [
            ['user_id', '=', $user_id],
            ['app_name', '=', $app_name],
        ];
        if (!empty($module_options['project_name'])) {
            $where[] = ['project_name_v', '=', $module_options['project_name']];
        }
        if (!empty($module_options['module_name'])) {
            if (is_array($module_options['module_name'])) {
                $model->whereIn('module_name', $module_options['module_name']);
            } else {
                $where[] = ['module_name', '=', $module_options['module_name']];
            }
        }
        if (!empty($module_options['page_name'])) {
            $where[] = ['page_name', '=', $module_options['page_name']];
        }
        if (!empty($module_options['position'])) {
            $where[] = ['position', '=', $module_options['position']];
        }
        if (!empty($module_options['module_tag'])) {
            $where[] = ['module_tag', '=', $module_options['module_tag']];
        }
        array_map(function ($v) use ($model) {
            call_user_func_array([$model, 'where'], $v);
        }, $where);
        $count = $model->count();
        $obj = $model->forPage($page_options['page'], $page_options['page_size']);
        $items = $obj->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }
}
