<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use App\Classes\Utils\UtilsCommon as UtilsCommon;
use Illuminate\Http\Request;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\BackendWithModuleXml\Basic\AppPageService;

class ToolsController extends Controller
{

    public function getRouterConfig(Request $request)
    {
        $module_service = app(ModuleService::class);
        $vendor_id = $request->input('export_vendor_id');
        $app_name = $request->input('export_app_name');
        $is_for_template = !!$request->input('is_for_template');
        $page_api_data = $module_service->getProjectAllPagesInfo($vendor_id, $app_name);
        $page_module_infos = [];
        if (empty(getOriginEnv('DEBUG_ROUTER'))) {
            return '';
        }
        foreach ($page_api_data as $item) {
            $page_module_infos[$item['path']] = $item['modules'];
        }
        $modules = [];
        foreach ($page_module_infos as $page_module_info) {
            UtilsCommon::reduceModule($page_module_info, function ($json_item) use (&$modules) {
                $modules[] = $json_item;
            });
        }
        $page_mapping = [];
        if ($is_for_template) {
            $page_mapping = $this->getPageInfoMapping($vendor_id, $app_name);
        }
        $modules_data = UtilsCommon::getModulesData($vendor_id, $app_name, $modules);
        foreach ($page_module_infos as &$page_module_info) {
            UtilsCommon::reduceModuleAndModOrigin($page_module_info, function (&$json_item) use ($modules_data) {
                $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                $json_item['module_data'] = $modules_data[$key] ?? [];
            });
        }
        foreach ($page_api_data as &$item) {
            if (empty($page_module_infos[$item['path']])) {
                continue;
            }
            $item['modules'] = $page_module_infos[$item['path']];
            $key = trim(str_replace('/', '-', $item['path']), '-');
            $page_row = $page_mapping[$key] ?? [];
            if ($is_for_template) {
                $item['page_name_cn'] = $page_row->page_name_cn ?? '';
                $item['page_des'] = $page_row->page_des ?? '';
            }
        }
        return json_encode(array_values($page_api_data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function getPageInfoMapping($user_id, $app_name)
    {
        $page_options = [
            'page' => 1,
            'page_size' => 60
        ];
        $filter_options = [
            'page_type' => AppPageService::PAGE_TYPE_SYSTEM
        ];
        $ret = app(AppPageService::class)->getList($user_id, $app_name, $page_options, $filter_options);
        $ret = $ret['data'] ?: [];
        $mapping = [];
        foreach ($ret as $item) {
            $mapping[$item->page_name] = $item;
        }
        return $mapping;
    }

}
