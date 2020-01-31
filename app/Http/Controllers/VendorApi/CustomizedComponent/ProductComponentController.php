<?php

namespace App\Http\Controllers\VendorApi\CustomizedComponent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\Goods\GoodsService;
use App\Classes\Services\Vendor\VendorService;
use App\Classes\Services\Module\XmlModuleService;

class ProductComponentController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function orderModuleProduct(Request $request)
    {
        $type = $request->input('type');
        $order_type = $request->input('orderType');
        $id = $request->input('id');
        if (!in_array($type, ['item', 'brand', 'supplier'])) {
            return app('app.response')->jsonError('非法參數:type');
        }
        if (!in_array($order_type, ['ToTop', 'ToBottom', 'ToPrev', 'ToNext'])) {
            return app('app.response')->jsonError('非法參數:orderType');
        }
        $login_vendor_id = $request->input('login_vendor_id');
        $ret = app(ModuleService::class)->orderModuleItems($login_vendor_id, $type, $id, $order_type);
        if (true === $ret['error']) {
            return app('app.response')->jsonError('更新失敗');
        }
        return app('app.response')->jsonSuccess('更新成功');
    }

    public function itemQuery(Request $request, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $goods_service = app(GoodsService::class);
        $shop_id = $request->input('shop_id', 0);
        $store_id = $request->input('store_id', 0);
        $product_id = $request->input('product_id', 0);
        $page = $request->input('page', 1);
        $page_size = 20;
        $user_id = $request->input('login_vendor_id');
        // 超级账号可获取默认重要的商家列表 商家级联店铺列表，通过商家ID，店铺ID查找，普通账号只能列出当前账号的店铺列表
        $vendor_list = [];
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        $choose_count = 0;
        if ('all' === $request->input('item_condition')) {
            $options = [
                'vendor_id' => $user_id,
                'shop_id' => $shop_id,
                'store_id' => $store_id,
                'product_id' => $product_id,
            ];
            $items = $goods_service->getItems($options, $page, $page_size);
            $product_ids = [];
            foreach ($items['data'] as $row) {
                $product_ids[] = $row['item_id'];
            }
            $checked_products = $module_service->moduleProductByProductIds($module_row_id, $product_ids);
            $checked_mapping = [];
            $product_id_2_id = [];
            foreach ($checked_products as $row) {
                $product_id_2_id[$row->product_id] = $row->id;
                $checked_mapping[$row->product_id] = $row->product_id;
            }
            foreach ($items['data'] as &$row) {
                $row['checked'] = 0;
                if (!empty($checked_mapping[$row['item_id']])) {
                    $row['checked'] = 1;
                    $row['id'] = $product_id_2_id[$row['item_id']];
                }
                $product_ids[] = $row['item_id'];
            }
            $choose_count = $module_service->moduleProductCount($module_row_id);
        }
        if ('choose' === $request->input('item_condition')) {
            $order = [
                ['order_num', 'asc']
            ];
            $items = $module_service->moduleProductByPage($module_row_id, $page, $page_size, $order);
            $choose_count = $items['total'];
        }
        $items['choose_count'] = $choose_count;
        $items['vendor_list'] = $vendor_list;
        return app('app.response')->jsonSuccess($items);
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
     * @throws \Throwable
     */

    public function getModuleEditHtml(Request $request, $app_name, $project_name, $module_name, $page_name, $position, $module_type_name)
    {
        $user_id = $request->input('login_vendor_id');
        $action = '/api/module_api/design/savemodule/' . $app_name . '/' . $project_name . '/' . $module_name . '/' . $page_name . '/' . $position;
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        $row = $module_service->getModuleData($module_row_id);
        $data = empty($row->json_data) ? [] : json_decode($row->json_data, true);
        $html = '';
        switch ($module_name) {
            // 表示直接弹出编辑器 而不是xml表单
            case 'cshop-com_self_defined':
                $view['action'] = $action;
                $view['data'] = $data;
                $html = view('module_components/cshop-com_self_defined', $view)->render();
                break;
            case 'cshop-com_self_defined_stable':
                $view['action'] = $action;
                $view['data'] = $data;
                $html = view('module_components/cshop-com_self_defined', $view)->render();
                break;
        }
        if (!$html) {
            $amv_path = AMVPHP_PATH;
            if ('react' === $module_type_name) {
                $amv_path = AMV_PATH;
            }
            $xml_path = $amv_path . '/' . $project_name . '/' . $module_name . '/module.xml';
            $xmlstring = file_get_contents($xml_path);
            $actionBase = '';
            $html = app(XmlModuleService::class)->xmlTable($xmlstring, $action, $actionBase, (array)$data);;
        }
        return app('app.response')->jsonSuccess($html);
    }

    public function chooseRemove(Request $request, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $product_id = $request->input('product_id');
        $trigger_name = $request->input('trigger');
        $type = $request->input('type');
        $module_service = app(ModuleService::class);
        $user_id = $request->input('login_vendor_id');
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position);
        if ('remove' === $type) {
            $ret = $module_service->removeOneProduct($user_id, $module_row_id, $trigger_name, $product_id);
        } else {
            $ret = $module_service->chooseOneProduce($user_id, $module_row_id, $trigger_name, $product_id);
        }
        if ($ret) {
            return app('app.response')->jsonSuccess();
        }
        return app('app.response')->jsonError('保存失敗');
    }
}
