<?php
/**
 * 商家后台基础页面
 */

namespace App\Http\Controllers\VendorWeb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\BackendWithModuleXml\Basic\AppPageService;
use App\Classes\Utils\UtilsCommon;
use App\Classes\Utils\HttpAuth;

class IndexController extends Controller
{
    public function mediaIndex(Request $request)
    {
        $view['title'] = '素材库';
        $view['design'] = true;
        $view['admin'] = false;
        $view['app_name'] = 'default';
        $view['login_vendor_id'] = $request->input('login_vendor_id');
        $modules = [
            'hd' => [],
            'bd' => [
                [
                    'main' => [
                        ['project_name' => 'uploadzone', 'module_name' => 'top_part', 'page_name' => 'index', 'position' => 1],
                        ['project_name' => 'uploadzone', 'module_name' => 'file_list', 'page_name' => 'index', 'position' => 1]

                    ]
                ]
            ],
            'ft' => []
        ];
        // 调用公共页面渲染视图时候 根据kissy_use参数直接输出需要执行的JS模块
        $page_from = 'upload_zone_home';
        $view['kissy_use'] = '
        webConfig.pageFrom = "' . $page_from . '";
        KISSY.use("modules/file_upload/upload_zone", function (S, UploadZone) {
            UploadZone.init();
        });
        ';
        $options = [
            'admin' => $view['admin'],
            'design' => $view['design'],
            'modules' => $modules,
        ];
        $view['page_modules_html_options'] = $options;
        $view['not_show_main_nav'] = true;
        request()->request->set('inner_page_from', $page_from);
        return view('module/page_builder_common', $view);
    }

    public function exampleApp(Request $request)
    {
        $view['title'] = '案例展示';
        $view['design'] = true;
        $view['admin'] = false;
        $view['app_name'] = 'default';
        $view['login_vendor_id'] = $request->input('login_vendor_id');
        $modules = [
            'hd' => [],
            'bd' => [
                [
                    'main' => [
                        ['project_name' => 'sys_admin', 'module_name' => 'example_app', 'page_name' => 'index', 'position' => 1],

                    ]
                ]
            ],
            'ft' => []
        ];
        // 调用公共页面渲染视图时候 根据kissy_use参数直接输出需要执行的JS模块
        $page_from = 'example_app_home';
        $view['kissy_use'] = '';
        $options = [
            'admin' => $view['admin'],
            'design' => $view['design'],
            'modules' => $modules,
        ];
        $view['page_modules_html_options'] = $options;
        $view['not_show_main_nav'] = true;
        request()->request->set('inner_page_from', $page_from);
        return view('module/page_builder_common', $view);
    }

    public function createApp(Request $request)
    {
        $view = [];
        $view['title'] = '应用管理';
        $view['project_name'] = 'sys_admin';
        $view['module_name'] = 'sys_admin_add_app';
        $view['not_show_main_nav'] = true;
        $view['design'] = true;
        $view['admin'] = true;
        $view['app_name'] = '';
        $view['app_list'] = [
            'data' => [],
            'total' => 0
        ];
        $view['form_action'] = getOriginEnv('AMOD_API_CMS_BASE_URL') . 'api/module_api/create_app/get_html';
        $user_id = $request->input('login_vendor_id');
        $page_options = ['page' => 1, 'page_size' => 50];
        $ret = app(AppService::class)->getList($user_id, $page_options);
        $view['app_list'] = $ret;
        $view['login_vendor_id'] = $user_id;
        return view('admin/create_app', $view);
    }

    public function createAppPage(Request $request, $app_name)
    {
        $view = [];
        $page = $request->input('page', 1);
        $page_size = 20;
        $page_options['page'] = $page;
        $page_options['page_size'] = $page_size;
        $user_id = $request->input('login_vendor_id');
        $page_options = ['page' => 1, 'page_size' => 50];
        $page_list = app(AppPageService::class)->getList($user_id, $app_name, $page_options);
        $view['login_vendor_id'] = $user_id;
        $view['layout_project_name'] = 'sys_admin';
        $view['layout_module_name'] = 'sys_admin_page_layout';
        $view['page_list'] = $page_list;
        $view['app_name'] = $app_name;
        $view['form_action'] = getOriginEnv('AMOD_API_CMS_BASE_URL') . 'api/module_api/create_app_page/get_html/' . $app_name;
        $view['layout_form_action'] = getOriginEnv('AMOD_API_CMS_BASE_URL') . 'api/module_api/create_app_page/get_layout_html/' .
            $app_name . '/';
        $view['admin'] = true;
        $view['design'] = true;
        $app_info = app(AppService::class)->getOne($user_id, $app_name);
        $view['title'] = (empty($app_info->app_name_cn) ? $app_info->app_name : $app_info->app_name_cn) . '页面管理';
        $view['app_info'] = $app_info;
        $is_set_app_cookie = false;
        $view['shop_vendor_token'] = '';
        if ($app_info->app_domain !== UtilsCommon::getDomainByBaseUrl(getOriginEnv('AMOD_FRONT_BASE_URL'))) {
            // 该应用未绑定域名 需要对前台域名设置cookie 写入APP_NAME VENDOR_ID JWT
            $is_set_app_cookie = true;
            $shop_vendor_token = HttpAuth::getAmodAppVendorAuth($user_id, $app_name);
            $view['shop_vendor_token'] = $shop_vendor_token;
        }
        $view['is_set_app_cookie'] = $is_set_app_cookie;
        return view('admin/create_app_page', $view);
    }

    public function appModuleList(Request $request, $app_name, $module_name = '')
    {
        $view = [];
        $page = $request->input('page', 1);
        $page_size = 20;
        $user_id = $request->input('login_vendor_id');
        $module_service = app(ModuleService::class);
        $project_name = 'public_project';
        $module_name_list = config('module_list_page.show_modules');
        if (!$module_name) {
            $module_name = array_values($module_name_list);
        }
        $module_list = $module_service->getListByModuleNames($project_name, array_values($module_name_list));
        $module_options['project_name'] = $project_name;
        $module_options['module_name'] = $module_name;
        $page_options['page'] = $page;
        $page_options['page_size'] = $page_size;
        $view['page_list'] = $module_service->getUniqueModuleListByModule($user_id, $app_name, $module_options, $page_options);
        $view['admin'] = true;
        $view['design'] = true;
        $view['app_name'] = $app_name;
        $view['project_name'] = $project_name;
        $view['module_name_list'] = $module_list;
        $view['login_vendor_id'] = $user_id;
        $app_info = app(AppService::class)->getOne($user_id, $app_name);
        $view['title'] = (empty($app_info->app_name_cn) ? $app_info->app_name : $app_info->app_name_cn) . '活动管理';
        $view['shop_vendor_token'] = HttpAuth::getAmodAppVendorAuth($user_id, $app_name);
        return view('admin/project_module_list', $view);
    }

    public function appProjectModuleList(Request $request, $app_name, $project_name, $module_name = '')
    {
        $view = [];
        $page = $request->input('page', 1);
        $page_size = 20;
        $user_id = $request->input('login_vendor_id');
        $module_service = app(ModuleService::class);
        $module_name_list = [];
        $module_list = $module_service->getListByModuleNames($project_name, array_values($module_name_list));
        $module_options['project_name'] = $project_name;
        $page_options['page'] = $page;
        $page_options['page_size'] = $page_size;
        $view['page_list'] = $module_service->getUniqueModuleListByModule($user_id, $app_name, $module_options, $page_options);
        $view['admin'] = true;
        $view['design'] = true;
        $view['app_name'] = $app_name;
        $view['project_name'] = $project_name;
        $view['module_name_list'] = $module_list;
        $view['login_vendor_id'] = $user_id;
        $app_info = app(AppService::class)->getOne($user_id, $app_name);
        $view['title'] = (empty($app_info->app_name_cn) ? $app_info->app_name : $app_info->app_name_cn) . '活动管理';
        $view['shop_vendor_token'] = HttpAuth::getAmodAppVendorAuth($user_id, $app_name);
        return view('admin/project_module_list', $view);
    }

}
