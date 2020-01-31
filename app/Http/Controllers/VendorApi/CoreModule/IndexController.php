<?php

namespace App\Http\Controllers\VendorApi\CoreModule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Utils\UtilsCommon as UtilsCommon;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\Module\ModuleDataCheckService;
use App\Classes\Services\Module\XmlModuleService;
use ES\Log\FluentLogger;

class IndexController extends Controller
{
    public function getModulesList(Request $request, $project_name)
    {
        $page_size = $request->input('page_size', 20);
        if ($page_size > 60) {
            $page_size = 60;
        }
        $page_options = [
            'page' => $request->input('page', 1),
            'page_size' => $page_size,
        ];
        $res = app(ModuleService::class)->getModuleBasicByProjectName($project_name, $page_options);
        return app('app.response')->jsonSuccess($res);
    }

    /**
     * 返回一个模块的HTML
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @param $module_type_name
     * @param $module_unique_id
     * @return mixed|string
     */
    public function getModuleHtml(Request $request, $app_name, $project_name, $module_name, $page_name, $position, $module_type_name, $module_unique_id)
    {
        $json_item['project_name'] = $project_name;
        $json_item['module_name'] = $module_name;
        $json_item['page_name'] = $page_name;
        $json_item['position'] = $position;
        $login_vendor_id = $request->input('login_vendor_id');
        $content = '';
        if ('react' === $module_type_name) {
            $module_content = \App\Classes\Widget\Widget::widget($login_vendor_id, $app_name, $json_item);
            $json_item['module_data'] = $json_item['module_data'] ?? [];
            $json_item['module_data'] = array_merge($json_item['module_data'], $module_content);
            $rjs = new \App\Classes\Utils\ComponentReactJS();
            $js_all = $rjs->init_js;
            $ret = UtilsCommon::getReactModuleHtml($rjs, $json_item, '/');
            $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
            if (false === $ret['error']) {
                $js_all .= $ret['data'];
                if ('window' === \App\Classes\Utils\ComponentReactJS::$env) {
                    $content = '<script>' . $js_all . ';$("#' . $module_unique_id . '").html(window["rendorBox"]["' . $key . '"]);</script>';
                } else {
                    $js_all .= 'print(JSON.stringify(global.rendorBox))';
                    $execute_ret = $rjs->executeJS($js_all);
                    $execute_ret = json_decode($execute_ret, true);
                    $content = $execute_ret[$key];
                }
            }
        } elseif ('common' === $module_type_name) {
            $content = UtilsCommon::getCommonModuleHtml($json_item, $login_vendor_id);
        } else {
            return '未知模块类型:module_type_name';
        }
        return $content;
    }

    /**
     * 返回一个模块的后台页面
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @param $module_type_name
     * @return string
     */
    public function getModuleEditHtml(Request $request, $app_name, $project_name, $module_name, $page_name, $position, $module_type_name)
    {
        try {
            $user_id = $request->input('login_vendor_id');
            $action = '/api/module_api/design/savemodule/' . $app_name . '/' . $project_name . '/' . $module_name . '/' . $page_name . '/' . $position;
            $module_service = app(ModuleService::class);
            $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
            $row = $module_service->getModuleData($module_row_id);
            $data = empty($row->json_data) ? [] : json_decode($row->json_data, true);
            $html = '';
            switch ($module_name) {
                // 表示直接弹出编辑器 而不是xml表单
                case 'self_defined_edit':
                    $view['action'] = $action;
                    $view['data'] = $data;
                    $html = view('module_components/self_defined', $view)->render();
                    break;
                case 'self_defined':
                    $view['action'] = $action;
                    $view['data'] = $data;
                    $html = view('module_components/self_defined', $view)->render();
                    break;
            }
            if (!$html) {
                $amv_path = AMVPHP_PATH;
                if ('react' === $module_type_name) {
                    $amv_path = AMV_PATH;
                }
                $xml_path = $amv_path . '/' . $project_name . '/' . $module_name . '/module.xml';
                $xml_string = file_get_contents($xml_path);
                $xml_string = $xml_string ?: '';
                $actionBase = '';
                $html = app(XmlModuleService::class)->xmlTable($xml_string, $action, $actionBase, (array)$data);;
            }
        } catch (\Exception|\Throwable $e) {
            $html = '';
            app(FluentLogger::class)->error('common', ['messgae' => $e->getMessage()]);
        }
        return app('app.response')->jsonSuccess($html);
    }


    /**
     * 返回一个商品补充信息的后台页面
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @param $product_id
     * @param $trigger_name
     * @param $module_type_name
     * @return string
     */
    public function getProductModuleEditHtml(Request $request, $app_name, $project_name, $module_name, $page_name, $position, $product_id, $trigger_name, $module_type_name)
    {
        $action = '/api/module_api/design/save_one_module_product/' . $app_name . '/' . $project_name . '/' . $module_name . '/' . $page_name . '/' . $position . '/' . $product_id . '/' . $trigger_name;
        $user_id = $request->input('login_vendor_id');
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        $row = $module_service->getOneModuleProduct($module_row_id, $product_id);
        $form_data = empty($row->extend) ? [] : json_decode($row->extend, true);
        $data = app(XmlModuleService::class)->getModuleProductEditHtml($module_type_name, $action, $project_name, $module_name, $form_data);
        if (true === $data['error']) {
            return app('app.response')->jsonSuccess('');
        }
        return app('app.response')->jsonSuccess($data['message']);
    }

    /**
     * 保存模块商品数据
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @param $product_id
     * @param $trigger_name
     * @return string
     */
    public function saveOneModuleProduct(Request $request, $app_name, $project_name, $module_name, $page_name, $position, $product_id, $trigger_name)
    {
        $user_id = $request->input('login_vendor_id');
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        if (!$module_row_id) {
            return app('app.response')->jsonError('模塊獲取異常');
        }
        $ret = $module_service->saveOneModuleProduct($module_row_id, $product_id, $trigger_name, UtilsCommon::getModulePost());
        if ($ret) {
            return app('app.response')->jsonSuccess();
        }
        return app('app.response')->jsonError('保存失敗');
    }

    /**
     * 保存模块数据
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @return string
     */
    public function saveModule(Request $request, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $user_id = $request->input('login_vendor_id');
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        if (!$module_row_id) {
            return app('app.response')->jsonError('模塊獲取異常');
        }
        $post_data = UtilsCommon::getModulePost();
        $ret = ModuleDataCheckService::checkModuleData($project_name, $module_name, $post_data);
        if (true === $ret['error']) {
            return app('app.response')->jsonError($ret['message']);
        }
        $ret = $module_service->saveModule($module_row_id, UtilsCommon::getModulePost());
        if ($ret) {
            return app('app.response')->jsonSuccess();
        }
        return app('app.response')->jsonError('保存失敗');
    }
}
