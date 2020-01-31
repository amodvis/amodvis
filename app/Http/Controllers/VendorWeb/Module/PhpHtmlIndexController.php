<?php

/**
 * 商家后台 PHP-HTML组装页面
 */


namespace App\Http\Controllers\VendorWeb\Module;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhpHtmlIndexController extends Controller
{
    /**
     * 返回具体小部件的html代码 默认不包括CSS，JS与html，body标签
     * @param Request $request
     * @param $project_name
     * @param $module_name
     * @param $page_name
     * @param $position
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getItem(Request $request, $project_name, $module_name, $page_name, $position)
    {
        $design = $request->input('design') ? true : false;
        $admin = $request->input('admin') ? true : false;
        $view['login_vendor_id'] = $request->input('login_vendor_id');
        // 开启design可以看到完整的显示
        $view['project_name'] = $project_name;
        $view['page_name'] = $page_name;
        $view['position'] = $position;
        $view['module_name'] = $module_name;
        $json_item = [
            'project_name' => $project_name,
            'module_name' => $module_name,
            'page_name' => $page_name,
            'position' => $position,
        ];
        $view['json_item'] = $json_item;
        $view['design'] = $design;
        $view['admin'] = $admin;
        return view('module/get_item_common', $view);
    }

}
