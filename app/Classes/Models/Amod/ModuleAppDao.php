<?php

namespace App\Classes\Models\Amod;

use Illuminate\Support\Facades\DB;

/**
 * 用于管理项目的页面
 * Class ModuleAppsDao
 * @package App\Classes\Models\Amod
 */
class ModuleAppDao
{

    public function getOneByWhere($where)
    {
        $model = DB::table('amod_app');
        $search_where = [];
        foreach ($where as $key => $value) {
            $item = [$key, '=', $value];
            $search_where[] = $item;
        }
        array_map(function ($v) use ($model) {
            call_user_func_array([$model, 'where'], $v);
        }, $search_where);
        return $model->first();
    }

    public function getList($user_id, $page_options)
    {
        $model = DB::table('amod_app');
        $where = [
            ['user_id', '=', $user_id],
        ];
        array_map(function ($v) use ($model) {
            call_user_func_array([$model, 'where'], $v);
        }, $where);
        $count = $model->count();
        $obj = $model->forPage($page_options['page'], $page_options['page_size']);
        $obj->orderBy('id', 'asc');
        $items = $obj->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }

    public function upset($user_id, $app_name, $app_view_type, $from_template,
                          $app_key, $app_domain, $app_name_cn, $des, $head_content = '', $foot_content = '')
    {
        $sql = 'insert into `amod_app` (`user_id`,`app_name`,`app_view_type`,`from_template`,`app_key`,`app_domain`,`app_name_cn`,
`des`,`head_content`,`foot_content`) value(
        :user_id,
        :app_name,
        :app_view_type,
        :from_template,
        :app_key,
        :app_domain,
        :app_name_cn,
        :des,
        :head_content,
        :foot_content
        ) on duplicate key update `app_name_cn`=:app_name_cn_update,`des`=:des_update,`app_key`=:app_key_update,
        `app_domain`=:app_domain_update,
        `head_content`=:head_content_update,`foot_content`=:foot_content_update
        ';
        return DB::insert($sql, [$user_id, $app_name, $app_view_type, $from_template,
            $app_key, $app_domain, $app_name_cn, $des,
            $head_content, $foot_content, $app_name_cn, $des,
            $app_key, $app_domain,
            $head_content, $foot_content]);
    }
}
