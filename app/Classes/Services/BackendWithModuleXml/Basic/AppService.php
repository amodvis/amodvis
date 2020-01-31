<?php

namespace App\Classes\Services\BackendWithModuleXml\Basic;

use App\Classes\Models\Amod\ModuleAppDao;
use  App\Classes\Utils\UtilsCommon;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\Module\EcGeneralAppConfigService;

class AppService
{
    const APP_CONFIG_ROUTER_PATH_NAME = 'app_router_config_template';

    const APP_VIEW_TYPE_PC = 1;
    const APP_VIEW_TYPE_MOBILE = 2;

    public function upset($user_id, $app_name, $app_view_type, $from_template,
                          $app_key, $app_domain, $app_name_cn, $des, $head_content = '', $foot_content = '')
    {
        return app(ModuleAppDao::class)->upset($user_id, $app_name, $app_view_type, $from_template,
            $app_key, $app_domain, $app_name_cn, $des,
            $head_content, $foot_content
        );
    }

    public function getList($user_id, $page_options)
    {
        return app(ModuleAppDao::class)->getList($user_id, $page_options);
    }

    public function getOne($user_id, $app_name)
    {
        $where = [
            'user_id' => $user_id,
            'app_name' => $app_name,
        ];
        if (!$user_id || !$app_name) {
            return [];
        }
        return app(ModuleAppDao::class)->getOneByWhere($where);
    }

    public function hdFtConfigSet($from_template)
    {
        $hd = '';
        $ft = '';
        switch ($from_template) {
            case 'ec_general':
                $hd = app(EcGeneralAppConfigService::class)->hdContent();
                $ft = app(EcGeneralAppConfigService::class)->ftContent();
                break;
            default:
                break;
        }
        return [$hd, $ft];
    }

    public function getOneByDomain($app_domain)
    {
        if (!$app_domain) {
            return [];
        }
        return app(ModuleAppDao::class)->getOneByWhere(['app_domain' => $app_domain]);
    }

    public function addDefaultPages($user_id, $from_template, $app_name)
    {
        $create_app_default_pages = file_get_contents(config_path() . '/' . self::APP_CONFIG_ROUTER_PATH_NAME .
            '/' . $from_template . '.json');
        $create_app_default_pages = json_decode($create_app_default_pages, true);
        foreach ($create_app_default_pages as $item) {
            $page_name = $item['path'];
            $page_name = trim(str_replace('/', '-', $page_name), '-');
            $modules_origin = $item['modules'];
            $no_module_data = $this->filterModuleConfig($modules_origin);
            $hd = $no_module_data['hd'] ?? [];
            $bd = $no_module_data['bd'] ?? [];
            $ft = $no_module_data['ft'] ?? [];
            $page_name_cn = $item['page_name_cn'] ?? '';
            $is_pull_update = intval($item['is_pull_update'] ?? 0);
            $is_hide_page = intval($item['is_hide_page'] ?? 1);
            $is_pre_load = intval($item['is_pre_load'] ?? 0);
            $is_cnd_cache = intval($item['is_cnd_cache'] ?? 0);
            $des = $item['page_des'] ?? '';
            app(AppPageService::class)->upset($user_id, $app_name, $page_name, $page_name_cn,
                $des, AppPageService::PAGE_TYPE_SYSTEM, $is_pull_update, $is_hide_page,
                $is_pre_load, $is_cnd_cache
            );
            $layout_json = [
                'hd' => json_encode($hd),
                'bd' => json_encode($bd),
                'ft' => json_encode($ft),
            ];
            app(AppPageService::class)->updateLayout($user_id, $app_name, $page_name, $layout_json);
            $this->saveDefaultModule($user_id, $app_name, $modules_origin);
        }
    }

    private function filterModuleConfig($modules)
    {
        UtilsCommon::reduceModuleAndModOrigin($modules, function (&$json_item) {
            unset($json_item['module_data']);
        });
        return $modules;
    }

    private function saveDefaultModule($user_id, $app_name, $modules)
    {
        $module_service = app(ModuleService::class);
        UtilsCommon::reduceModuleAndModOrigin($modules, function ($json_item) use (
            $user_id, $module_service,
            $app_name
        ) {
            $module_row_id = $module_service->getModuleRowId($user_id, $app_name,
                $json_item['project_name'],
                $json_item['module_name'],
                $json_item['page_name'],
                $json_item['position']
            );
            $module_service->saveModule($module_row_id, $json_item['module_data'] ?? []);
        });
    }
}
