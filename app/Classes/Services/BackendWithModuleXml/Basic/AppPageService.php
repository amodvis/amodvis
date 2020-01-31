<?php

namespace App\Classes\Services\BackendWithModuleXml\Basic;

use App\Classes\Models\Amod\ModuleAppPageDao;

class AppPageService
{
    public const PAGE_TYPE_SYSTEM = 1;
    public const PAGE_TYPE_SELF = 2;

    public function upset($user_id, $app_name, $page_name, $page_name_cn, $des, $page_type = 2, $is_pull_update = 0
        , $is_hide_page = 0, $is_pre_load = 0, $is_cdn_cache = 0, $is_user_auth = 1, $head_content = '', $foot_content = ''
    )
    {
        return app(ModuleAppPageDao::class)->upset($user_id, $app_name, $page_name, $page_name_cn, $des,
            $page_type, $is_pull_update, $is_hide_page, $is_pre_load, $is_cdn_cache, $is_user_auth, $head_content, $foot_content);
    }

    public function updateLayout($user_id, $app_name, $page_name, $layout_json)
    {
        $update_data = ['layout' => json_encode($layout_json)];
        return app(ModuleAppPageDao::class)->update($user_id, $app_name, $page_name, $update_data);
    }

    public function getList($user_id, $app_name, $page_options, $filter_options = [])
    {
        return app(ModuleAppPageDao::class)->getList($user_id, $app_name, $page_options, $filter_options);
    }

    public function getOne($user_id, $app_name, $page_name)
    {
        return app(ModuleAppPageDao::class)->getOne($user_id, $app_name, $page_name);
    }
}
