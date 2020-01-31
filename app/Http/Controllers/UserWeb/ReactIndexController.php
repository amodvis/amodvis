<?php
/**
 * REACT前台页面
 */

namespace App\Http\Controllers\UserWeb;

use App\Classes\Services\BackendWithModuleXml\Basic\AppPageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Utils\UtilsCommon as UtilsCommon;
use App\Classes\Services\Module\ModuleService;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;
use App\Classes\Utils\HttpAuth;
use Illuminate\Support\Facades\Redis;

class ReactIndexController extends Controller
{
    /**
     * 默认首页对应的页面page_name index
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontDefault(Request $request)
    {
        $page_name = 'index';
        return $this->frontIndex($request, $page_name);
    }

    /**
     * 商品详情页
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontProductDetail(Request $request, $product_id)
    {
        $page_name = 'product/:product_id';
        UtilsCommon::frameGlobalSet('product_id', $product_id);
        return $this->frontIndex($request, $page_name);
    }

    /**
     * 关键词搜索结果页
     * @param Request $request
     * @param $keyword
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontItemSearch(Request $request, $keyword)
    {
        $page_name = '/item_search/:keyword';
        UtilsCommon::frameGlobalSet('keyword', $keyword);
        return $this->frontIndex($request, $page_name);
    }

    public function orderConfirm(Request $request)
    {
        $page_name = '/order/confirm/:cart_item';
        return $this->frontIndex($request, $page_name);
    }


    /**
     * 文章详情页
     * @param Request $request
     * @param $article_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontArticleDetail(Request $request, $article_id)
    {
        $page_name = 'article/:article_id';
        UtilsCommon::frameGlobalSet('article_id', $article_id);
        return $this->frontIndex($request, $page_name);
    }

    /**
     * 同构页面入口
     * @param Request $request
     * @param $page_name
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function frontIndex(Request $request, $page_name)
    {
        $view['page_name'] = '/' . trim($page_name, '/');
        $module_service = app(ModuleService::class);
        $vendor_id_by_domain = $request->input('vendor_id_by_domain');
        $app_name = $request->input('app_name_by_domain');
        $page_api_data = $module_service->getProjectAllPagesInfo($vendor_id_by_domain, $app_name);
        $page_module_infos = [];
        // 默认只返回当前项目当前页的所有模块的后台数据
        $is_only_this_page_data = true;
        if (getOriginEnv('DEBUG_ROUTER') && $request->input('debug_router')) {
            $is_only_this_page_data = false;
        }
        foreach ($page_api_data as $item) {
            if ($is_only_this_page_data) {
                if ($view['page_name'] === $item['path']) {
                    $page_module_infos[$item['path']] = $item['modules'];
                    break;
                }
            } else {
                $page_module_infos[$item['path']] = $item['modules'];
            }
        }
        $modules = [];
        foreach ($page_module_infos as $page_module_info) {
            UtilsCommon::reduceModule($page_module_info, function ($json_item) use (&$modules) {
                $modules[] = $json_item;
            });
        }
        $vendor_id_by_domain = $request->input('vendor_id_by_domain');
        $modules_data = UtilsCommon::getModulesData($vendor_id_by_domain, $app_name, $modules);
        foreach ($page_module_infos as &$page_module_info) {
            UtilsCommon::reduceModuleAndModOrigin($page_module_info, function (&$json_item) use ($modules_data) {
                $key = $json_item['project_name'] . '/' . $json_item['module_name'] . '/' . $json_item['page_name'] . '/' . $json_item['position'];
                $json_item['module_data'] = $modules_data[$key] ?? [];
            });
        }
        foreach ($page_api_data as &$item) {
            if (!empty($page_module_infos[$item['path']])) {
                $item['modules'] = $page_module_infos[$item['path']];
            }
        }
        // 获取APP信息
        $app_info = app(AppService::class)->getOne($vendor_id_by_domain, $app_name);
        $view['app_info'] = $app_info;
        // 获取APP PAGE信息
        $page_name = trim(str_replace('/', '-', $page_name), '-');
        $app_page_info = app(AppPageService::class)->getOne($vendor_id_by_domain, $app_name, $page_name);
        $view['app_page_info'] = $app_page_info;
        $view['page_api_data'] = json_encode(array_values($page_api_data), JSON_UNESCAPED_SLASHES);
        $view['app_name_by_domain'] = $app_name;
        $view['vendor_id_by_domain'] = $vendor_id_by_domain;

        // cache vendor app 防止每次刷新变化 导致CDN缓存失效
        $vendor_id_app_name_key = 'Auth:' . $vendor_id_by_domain . '--' . $app_name;
        $vendor_id_app_name = \RedisF::get($vendor_id_app_name_key);
        if (!$vendor_id_app_name) {
            $shop_vendor_token = HttpAuth::getAmodAppVendorAuth($vendor_id_by_domain, $app_name);
            \RedisF::setex($vendor_id_app_name_key, 3600, $shop_vendor_token);
        } else {
            $shop_vendor_token = $vendor_id_app_name;
        }
        $view['shop_vendor_token'] = $shop_vendor_token;
        // todo remove
        $view['vendor_id_by_domain'] = $view['shop_vendor_token'];
        return view('module/react_front_index', $view);
    }

}
