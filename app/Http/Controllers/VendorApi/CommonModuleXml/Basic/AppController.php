<?php

/**
 * 应用管理
 */

namespace App\Http\Controllers\VendorApi\CommonModuleXml\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;
use App\Classes\Services\Module\XmlModuleService;

class AppController extends Controller
{
    public function upset(Request $request)
    {
        $user_id = $request->input('login_vendor_id');
        $from_template = $request->input('from_template');
        $app_name = $request->input('app_name') ?: '';
        $app_name_cn = $request->input('app_name_cn') ?: '';
        $des = $request->input('des') ?: '';
        $app_domain = $request->input('app_domain') ?: '';
        $app_key = $request->input('app_key') ?: '';
        $head_content = $request->input('head_content') ?: '';
        $foot_content = $request->input('foot_content') ?: '';
        $is_insert = false;
        $app_service = app(AppService::class);
        if (!$request->query->get('app_name')) {
            $row = $app_service->getOne($user_id, $app_name);
            if (!empty($row)) {
                return app('app.response')->jsonError('APP已存在');
            }
            $is_insert = true;
        }
        if ($app_domain) {
            $row = $app_service->getOneByDomain($app_domain);
            if (!empty($row) && $row->user_id != $user_id) {
                // 已经被其他用户绑定
                return app('app.response')->jsonError('域名已綁定');
            }
            if (!empty($row) && $row->user_id == $user_id && $row->app_name != $app_name) {
                // 已经被当前用户其他应用绑定
                return app('app.response')->jsonError('域名已被您的其他應用綁定');
            }
        }
        if (!trim($head_content) && !trim($foot_content)) {
            list($head_content, $foot_content) = $app_service->hdFtConfigSet($from_template);
        }
        if (strstr($from_template, 'pc_') === $from_template) {
            $app_view_type = AppService::APP_VIEW_TYPE_PC;
        } elseif (strstr($from_template, 'mobile_') === $from_template) {
            $app_view_type = AppService::APP_VIEW_TYPE_MOBILE;
        }
        $app_service->upset($user_id, $app_name, $app_view_type, $from_template,
            $app_key, $app_domain, $app_name_cn, $des, $head_content, $foot_content);
        if (true === $is_insert) {
            // 创建默认的页面
            $app_service->addDefaultPages($user_id, $from_template, $app_name);
        }
        return app('app.response')->jsonSuccess('');
    }

    public function getHtml(Request $request)
    {
        $module_type_name = 'react';
        $project_name = 'sys_admin';
        $module_name = 'sys_admin_add_app';
        $app_name = $request->query->get('app_name', '');
        $query = $app_name ? '?app_name=' . $app_name : '';
        $action = '/api/module_api/create_app/upset' . $query;
        $user_id = $request->input('login_vendor_id');
        $app_name = $request->input('app_name', []);
        $form_data = [];
        if ($app_name) {
            $form_data = (array)app(AppService::class)->getOne($user_id, $app_name);
        }
        $data = app(XmlModuleService::class)->getModuleEditHtml($module_type_name, $action, $project_name, $module_name, $form_data);
        if (true === $data['error']) {
            return app('app.response')->jsonSuccess('');
        }
        return app('app.response')->jsonSuccess($data['message']);
    }

    public function getList(Request $request)
    {
        $user_id = $request->input('login_vendor_id');
        $page_options = ['page' => 1, 'page_size' => 50];
        $ret = app(AppService::class)->getList($user_id, $page_options);
        return app('app.response')->jsonSuccess($ret);
    }
}
