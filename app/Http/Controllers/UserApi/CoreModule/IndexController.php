<?php

namespace App\Http\Controllers\UserApi\CoreModule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Utils\UtilsCommon as UtilsCommon;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\Goods\GoodsService;

class IndexController extends Controller
{

    /**
     * 返回具体小部件的html代码 默认不包括CSS，JS与html，body标签
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getItem(Request $request, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $design = $request->input('design') ? true : false;
        $admin = $request->input('admin') ? true : false;
        $class_name = '\\Amv\\' . $project_name . '\\' . $module_name . '\\DefaultWidget';
        $options = $request->input('options');
        $options = json_decode($options, true) ?: [];
        $view['class_name'] = $class_name;
        $view['options'] = $options;
        $view['app_name'] = $app_name;
        $view['project_name'] = $project_name;
        $view['page_name'] = $page_name;
        $view['position'] = $position;
        $view['module_name'] = $module_name;
        $view['design'] = $design;
        $view['admin'] = $admin;
        $view['page_location'] = '/';
        $json_item = [
            'project_name' => $project_name,
            'module_name' => $module_name,
            'page_name' => $page_name,
            'position' => $position,
        ];
        $login_vendor_id = $request->input('shop_vendor_id');
        $module_data = \App\Classes\Widget\Widget::widget($login_vendor_id, $app_name, $json_item);
        $json_item['module_data'] = $module_data;
        $view['json_item'] = $json_item;
        return view('module/get_item_react_open', $view);
    }


    public function getModulesData(Request $request, $app_name)
    {
        $modules = $request->input('module_list');
        if (is_string($modules)) {
            $modules = json_decode($modules, true);
            $new_modules = [];
            foreach ($modules as $item) {
                $new_modules[] = ['project_name' => $item[0], 'module_name' => $item[1], 'page_name' => $item[2], 'position' => $item[3]];
            }
        } else {
            $new_modules = $modules;
        }
        $vendor_id = $request->input('shop_vendor_id');
        $is_advance = $request->input('is_advance');
        $ret = UtilsCommon::getModulesData($vendor_id, $app_name, $new_modules, !!$is_advance);
        return app('app.response')->jsonSuccess($ret);
    }

    public function getOneModuleData(Request $request, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $json_item = [
            'project_name' => $project_name,
            'module_name' => $module_name,
            'page_name' => $page_name,
            'position' => $position,
        ];
        $vendor_id = $request->input('shop_vendor_id');
        $module_content = \App\Classes\Widget\Widget::widget($vendor_id, $app_name, $json_item);
        $json_item['module_data'] = $json_item['module_data'] ?? [];
        $json_item['module_data'] = array_merge($json_item['module_data'], $module_content);
        return app('app.response')->jsonSuccess($json_item['module_data']);
    }

    /**
     * 获取模块的所有商品
     * 这里会返回模块的全部商品，可以自己加逻辑排除或者指定trigger_name对应的items
     * @param Request $request
     * @param $app_name
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @return string
     */
    public function getOneModuleProductByPage(Request $request, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 30);
        $user_id = $request->input('shop_vendor_id');
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        $order = [
            ['order_num', 'asc']
        ];
        $items = $module_service->moduleProductByPage($module_row_id, $page, $page_size, $order);
        return app('app.response')->jsonSuccess($items);
    }


    /**
     * 各参数组合起来可用于BI相关或者相关转换工作
     * @param Request $request
     * @return string
     */
    public function productList(Request $request)
    {
        $keyword = $request->input('keyword') ?: '';
        if ($keyword) {
            $options = [
                'keyword' => $keyword,
            ];
        }
        $goods_service = app(GoodsService::class);
        $page = $request->input('page') ?: 1;
        $page_size = $request->input('page_size') ?: 50;
        $vendor_id = request()->input('shop_vendor_id');
        if (!$vendor_id) {
            app('app.response')->jsonSuccess([
                'total' => 0,
                'data' => [],
            ]);
        }
        $options['vendor_id'] = $vendor_id;
        return app('app.response')->jsonSuccess($goods_service->getItems($options, $page, $page_size));
    }
}
