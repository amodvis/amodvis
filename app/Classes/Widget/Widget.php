<?php

namespace App\Classes\Widget;

use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\Facades\App;
use App\Classes\Services\Module\ModuleService;

abstract class Widget
{
    protected static $module_info = [];

    protected static $is_view_project = true;

    public function init()
    {
    }

    public function view($view_path, $page, $view = [])
    {
        $this->changeViewDir($view_path);
        $html = view($page, $view)->render();
        $this->changeViewDir(dirname(__DIR__, 3) . '/resources/views');
        return $html;
    }

    public function changeViewDir($path)
    {
        $path = [$path];
        $finder = new FileViewFinder(App::make('files'), $path);
        View::setFinder($finder);;
    }

    public abstract function run($view);

    public static function widget($user_id, $app_name, $options)
    {
        self::$module_info = [
            'user_id' => $user_id,
            'app_name' => $app_name,
            'project_name' => $options['project_name'],
            'module_name' => $options['module_name'],
            'page_name' => $options['page_name'],
            'position' => $options['position'],
        ];
        $module_service = app(ModuleService::class);
        $module_row_id = $module_service->getModuleRowId($user_id, $app_name, $options['project_name'],
            $options['module_name'], $options['page_name'], $options['position']);
        $module_data = $module_service->getModuleData($module_row_id);
        $module_data = empty($module_data->json_data) ? [] : $module_data->json_data;
        if ($module_data) {
            $module_data = json_decode($module_data, true);
        }
        $is_exclude_product = !!(request()->input('is_exclude_product'));
        $request_page_size = request()->input('page_size', 0);
        // 获取PRODUCT信息
        $is_for_template = !!request()->input('is_for_template');
        if (false === $is_exclude_product && !empty($module_data['item_exists']) && !$is_for_template) {
            $page = 1;
            $page_size = $module_data['page_size'] ?? 10;
            if ($request_page_size) {
                $page_size = $request_page_size;
            }
            $order = [['order_num', 'asc'], ['id', 'desc']];
            $product_ret = $module_service->moduleProductByPage($module_row_id, $page, $page_size, $order);
            $product_ret['product_trigger_mapping'];
            $product_data = $product_ret['data'];
            $product_total = $product_ret['total'];
            $product_mapping = [];
            $id_2_item_id = [];
            foreach ($product_data as $item) {
                $product_mapping[$item['item_id']] = $item;
                $id_2_item_id[$item['id']] = $item['item_id'];
            }
            $ret_trigger = [];
            foreach ($product_ret['product_trigger_mapping'] as $trigger_name => $product_ids) {
                $ret_trigger[$trigger_name] = [];
                foreach ($product_ids as $product_id) {
                    if (empty($product_mapping[$product_id])) {
                        continue;
                    }
                    $product_mapping[$product_id]['id'] = $id_2_item_id[$product_mapping[$product_id]['id']];
                    unset($product_mapping[$product_id]['item_id']);
                    $ret_trigger[$trigger_name][] = $product_mapping[$product_id] ?? [];
                }
            }
            $module_data = array_merge($module_data, $ret_trigger);
            $module_data['_total'] = $product_total;
        }
        $call_class = get_called_class();
        if ($call_class === self::class || $is_for_template) {
            // react 模块直接调用当前类，返回模块的数据
            return $module_data;
        }
        $instance = new $call_class;
        // 普通模块调用各自模块的run方法，可以进行特殊逻辑处理
        $module_data = is_array($module_data) ? $module_data : [];
        if ($call_class::$is_view_project) {
            return $instance->run(array_merge($module_data, ['kissy_use' => $options['kissy_use'] ?? '']));
        }
        return $instance->run($module_data);
    }
}
