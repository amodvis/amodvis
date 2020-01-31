<?php
/**
 * 表单特殊组件HTML接口 无XML后台配置
 */

namespace App\Http\Controllers\VendorApi\CustomizedComponent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HtmlController extends Controller
{
    /**
     * @return array|string
     * @throws \Throwable
     */
    public function itemChoose()
    {
        $view['title'] = '选择宝贝';
        return view('module_components/item_choose', $view)->render();
    }

    /**
     * @return array|string
     * @throws \Throwable
     */
    public function moduleChoose()
    {
        $view['title'] = '组件选择';
        return view('module_components/module_choose', $view)->render();
    }

    /**
     * @return array|string
     * @throws \Throwable
     */
    public function htmlEditor()
    {
        $view['title'] = '富文本编辑';
        return view('module_components/html_editor', $view)->render();
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function mediaSelector(Request $request)
    {
        $view['title'] = '素材库';
        $design = !!$request->input('design');
        $admin = !!$request->input('admin');
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
        $options = [
            'admin' => $admin,
            'design' => $design,
            'modules' => $modules,
            'kissy_use_mapping' => [
                'uploadzone/file_list' => '
                KISSY.use("modules/file_upload/upload_zone", function (S, UploadZone) {
                UploadZone.init();
                });
            '
            ]
        ];
        $view['page_modules_html_options'] = $options;
        $view['admin'] = $admin;
        $view['design'] = $design;
        return view('module_components/media_library', $view)->render();
    }
}
