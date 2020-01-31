<?php
/**
 * 商家后台 REACT组装页面
 */

namespace App\Http\Controllers\VendorWeb\Module;

use App\Classes\Services\BackendWithModuleXml\Basic\AppPageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;

class ReactIndexController extends Controller
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
        $login_vendor_id = $request->input('login_vendor_id');
        $module_data = \App\Classes\Widget\Widget::widget($login_vendor_id, $app_name, $json_item);
        $json_item['module_data'] = $module_data;
        $view['json_item'] = $json_item;
        return view('module/get_item_react', $view);
    }


    public function pageBuilderMobile(Request $request, $app_name, $page_name)
    {
        $login_vendor_id = $request->input('login_vendor_id');
        $view['login_vendor_id'] = $login_vendor_id;
        $design = true;
        $admin = true;
        $module_service = app(ModuleService::class);
        $page_api_data = $module_service->getProjectAllPagesInfo($login_vendor_id, $app_name, $page_name);
        if (empty($page_api_data)) {
            return '请配置布局';
        }
        $path = '/' . str_replace('-', '/', $page_name);
        $view['page_path'] = trim($path, '/');
        $view['app_name'] = $app_name;
        $page_api_data = $page_api_data[$path];
        $view['admin'] = $admin;
        $view['page_name'] = $page_name;
        $view['design'] = $design;
        // 获取APP信息
        $app_info = app(AppService::class)->getOne($login_vendor_id, $app_name);
        $view['app_info'] = $app_info;
        $view['modules'] = $page_api_data['modules'];
        $view['not_show_main_nav'] = true;
        return view('module/page_builder_mobile', $view);
    }

    public function pageBuilderMobileView(Request $request, $app_name, $page_name)
    {
        $login_vendor_id = $request->input('login_vendor_id');
        $view['login_vendor_id'] = $login_vendor_id;
        $design = true;
        $admin = true;
        $path = str_replace('-', '/', $page_name);
        $view['page_path'] = trim($path, '/');
        $view['app_name'] = $app_name;
        $view['admin'] = $admin;
        $view['page_name'] = $page_name;
        $view['design'] = $design;
        // 获取APP信息
        $app_info = app(AppService::class)->getOne($login_vendor_id, $app_name);
        $view['app_info'] = $app_info;
        $view['not_show_main_nav'] = true;
        return view('module/page_builder_mobile_view', $view);
    }

    /**
     * 页面组装
     * @param Request $request
     * @param $app_name
     * @param $page_name
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function pageBuilder(Request $request, $app_name, $page_name)
    {
        $login_vendor_id = $request->input('login_vendor_id');
        $view['login_vendor_id'] = $login_vendor_id;
        $design = true;
        $admin = true;
        $module_service = app(ModuleService::class);
        $page_api_data = $module_service->getProjectAllPagesInfo($login_vendor_id, $app_name, $page_name);
        if (empty($page_api_data)) {
            return '请配置布局';
        }
        $path = '/' . str_replace('-', '/', $page_name);
        $view['app_name'] = $app_name;
        $page_api_data = $page_api_data[$path];
        $options = [
            'admin' => $admin,
            'design' => $design,
            'modules' => $page_api_data['modules'],
            'is_advance' => false,
            'page_location' => $path,
        ];
        $view['admin'] = $admin;
        $view['design'] = $design;
        $view['page_modules_html_options'] = $options;
        // 获取APP信息
        $app_info = app(AppService::class)->getOne($login_vendor_id, $app_name);
        $view['app_info'] = $app_info;
        // 获取APP PAGE信息
        $page_name = trim(str_replace('/', '-', $page_name), '-');
        $app_page_info = app(AppPageService::class)->getOne($login_vendor_id, $app_name, $page_name);
        $view['app_page_info'] = $app_page_info;
        return view('module/page_builder_react', $view);
    }
}
