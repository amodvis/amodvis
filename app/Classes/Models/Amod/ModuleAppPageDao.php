<?php

namespace App\Classes\Models\Amod;

use Illuminate\Support\Facades\DB;

/**
 * 用于管理项目的页面
 * Class ModuleProductDao
 * @package App\Classes\Models\Amod
 */
class ModuleAppPageDao
{
    public function getList($user_id, $app_name, $page_options, $filter_options = [])
    {
        $model = DB::table('amod_app_page');
        $model->where('user_id', '=', $user_id);
        $model->where('app_name', '=', $app_name);
        foreach ($filter_options as $key => $val) {
            $model->where($key, '=', $val);
        }
        $count = $model->count();
        $obj = $model->forPage($page_options['page'], $page_options['page_size']);
        $obj->orderBy('page_type', 'asc');
        $obj->orderBy('id', 'asc');
        $items = $obj->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }

    public function getOne($user_id, $app_name, $page_name)
    {
        $model = DB::table('amod_app_page');
        if (!$user_id || !$app_name) {
            return [];
        }
        $where = [
            ['user_id', '=', $user_id],
            ['app_name', '=', $app_name],
            ['page_name', '=', $page_name],
        ];
        array_map(function ($v) use ($model) {
            call_user_func_array([$model, 'where'], $v);
        }, $where);
        return $model->first();
    }

    public function upset($user_id, $app_name, $page_name, $page_name_cn, $des, $page_type = 2, $is_pull_update = 1,
                          $is_hide_page = 0, $is_pre_load = 0, $is_cdn_cache = 0, $is_user_auth = 1,
                          $head_content = '', $foot_content = '')
    {
        $head_content = $head_content ?: '';
        $foot_content = $foot_content ?: '';
        $page_name_cn = $page_name_cn ?: "";
        $des = $des ?: "";
        $sql = 'insert into `amod_app_page` (`user_id`,`app_name`,`page_name`,`page_name_cn`,`des`,`page_type`,
        `is_pull_update`,`is_hide_page`,`is_pre_load`,`is_cdn_cache`,`is_user_auth`,`head_content`,`foot_content`) value(
        :user_id,
        :app_name,
        :page_name,
        :page_name_cn,
        :des,
        :page_type,
        :is_pull_update,
        :is_hide_page,
        :is_pre_load,
        :is_cdn_cache,
        :is_user_auth,
        :head_content,
        :foot_content
        ) on duplicate key update `page_name`=:page_name_update,`page_name_cn`=:page_name_cn_update,
        `des`=:des_update,`is_pull_update`=:is_pull_update_update,
        `is_hide_page`=:is_hide_page_update,`is_pre_load`=:is_pre_load_update,`is_cdn_cache`=:is_cdn_cache_update,`is_user_auth`=:is_user_auth_update,
        `head_content`=:head_content_update,`foot_content`=:foot_content_update';
        return DB::insert($sql, [$user_id, $app_name, $page_name, $page_name_cn, $des, $page_type, $is_pull_update,
            $is_hide_page, $is_pre_load, $is_cdn_cache, $is_user_auth,
            $head_content, $foot_content,
            $page_name, $page_name_cn, $des, $is_pull_update, $is_hide_page, $is_pre_load,
            $is_cdn_cache, $is_user_auth, $head_content, $foot_content]);
    }

    public function update($user_id, $app_name, $page_name, $update_data)
    {
        $model = DB::table('amod_app_page');
        return $model->where('user_id', '=', $user_id)
            ->where('app_name', '=', $app_name)
            ->where('page_name', '=', $page_name)
            ->update($update_data);
    }
}
