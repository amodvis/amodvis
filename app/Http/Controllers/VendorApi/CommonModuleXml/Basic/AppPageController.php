<?php

/**
 * 应用页面管理
 */

namespace App\Http\Controllers\VendorApi\CommonModuleXml\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Services\BackendWithModuleXml\Basic\AppPageService;
use App\Classes\Services\Module\XmlModuleService;
use App\Classes\Services\Module\ModuleDataCheckService;
use App\Classes\Utils\UtilsCommon as UtilsCommon;

class AppPageController extends Controller
{
    public function upset(Request $request, $app_name)
    {
        $user_id = $request->input('login_vendor_id');
        $page_name = $request->input('page_name') ?: '';
        $page_name = trim(str_replace('/', '-', $page_name), '-');
        $page_name_cn = $request->input('page_name_cn') ?: '';
        $des = $request->input('des') ?: '';
        $is_pull_update = $request->input('is_pull_update', 1);
        $is_hide_page = $request->input('is_hide_page', 0);
        $is_pre_load = $request->input('is_pre_load', 0);
        $is_cdn_cache = $request->input('is_cdn_cache', 0);
        $is_user_auth = $request->input('is_user_auth', 1);
        $head_content = $request->input('head_content') ?: '';
        $foot_content = $request->input('foot_content') ?: '';
        if (!$request->query->get('page_name')) {
            $row = app(AppPageService::class)->getOne($user_id, $app_name, $page_name);
            if (!empty($row)) {
                return app('app.response')->jsonError('APP已存在');
            }
        }
        $page_type = AppPageService::PAGE_TYPE_SELF;
        app(AppPageService::class)->upset($user_id, $app_name, $page_name, $page_name_cn, $des, $page_type,
            $is_pull_update, $is_hide_page, $is_pre_load, $is_cdn_cache, $is_user_auth, $head_content, $foot_content);
        return app('app.response')->jsonSuccess('');
    }

    public function getHtml(Request $request, $app_name)
    {
        $module_type_name = 'react';
        $project_name = 'sys_admin';
        $module_name = 'sys_admin_page_base_upset';
        $page_name = $request->query->get('page_name', '');
        $query = $page_name ? '?page_name=' . $page_name : '';
        $action = '/api/module_api/create_app_page/upset/' . $app_name . '/' . $query;
        $user_id = $request->input('login_vendor_id');
        $page_name = $request->input('page_name', []);
        $form_data = [];
        if ($page_name) {
            $form_data = (array)app(AppPageService::class)->getOne($user_id, $app_name, $page_name);
            $form_data['page_name'] = '/' . trim(str_replace('-', '/', $form_data['page_name']), '/');
        }
        $data = app(XmlModuleService::class)->getModuleEditHtml($module_type_name, $action, $project_name, $module_name, $form_data);
        if (true === $data['error']) {
            return app('app.response')->jsonSuccess('');
        }
        return app('app.response')->jsonSuccess($data['message']);
    }

    public function getList(Request $request, $app_name)
    {
        $user_id = $request->input('login_vendor_id');
        $page_options = ['page' => 1, 'page_size' => 50];
        $ret = app(AppPageService::class)->getList($user_id, $app_name, $page_options);
        return app('app.response')->jsonSuccess($ret);
    }

    public function updateLayout(Request $request, $app_name, $page_name)
    {
        $user_id = $request->input('login_vendor_id');
        $post_data = UtilsCommon::getModulePost();
        $project_name = 'sys_admin';
        $module_name = 'sys_admin_page_layout';
        $ret = ModuleDataCheckService::checkModuleData($project_name, $module_name, $post_data);
        if (true === $ret['error']) {
            return app('app.response')->jsonError($ret['message']);
        }
        app(AppPageService::class)->updateLayout($user_id, $app_name, $page_name, $post_data);
        return app('app.response')->jsonSuccess('操作成功');
    }


    public function getLayoutHtml(Request $request, $app_name, $page_name)
    {
        $module_type_name = 'react';
        $project_name = 'sys_admin';
        $module_name = 'sys_admin_page_layout';
        $action = '/api/module_api/create_app_page/update_layout/' . $app_name . '/' . $page_name;
        $user_id = $request->input('login_vendor_id');
        $form_data = [];
        if ($page_name) {
            $form_data = (array)app(AppPageService::class)->getOne($user_id, $app_name, $page_name);
            $layout = $form_data['layout'];
            if ($layout) {
                $form_data = json_decode($layout, true);
            }
        }
        $data = app(XmlModuleService::class)->getModuleEditHtml($module_type_name, $action, $project_name, $module_name, $form_data);
        if (true === $data['error']) {
            return app('app.response')->jsonSuccess('');
        }
        return app('app.response')->jsonSuccess($data['message']);
    }
}
