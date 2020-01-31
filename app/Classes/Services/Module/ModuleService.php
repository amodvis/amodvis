<?php

namespace App\Classes\Services\Module;

use App\Classes\Models\Amod\ModuleProductDao;
use App\Classes\Models\Amod\ModuleUniqueDao;
use App\Classes\Models\Amod\ModuleDataDao;
use App\Classes\Models\Amod\ModuleBasicDao;
use App\Classes\Services\Goods\GoodsService;
use App\Classes\Services\BackendWithModuleXml\Basic\AppPageService;
use Illuminate\Support\Facades\DB;
use App\Classes\Utils\HttpAuth;
use ES\Net\Http\FpfHttpClient;
use App\Classes\Consts\Module as ModuleConst;
use App;

class ModuleService
{
    public static $front_version = '';

    public function getFrontVersion()
    {
        if (App::environment(PROD)) {
            $ret = app(FpfHttpClient::class)->get('https://amodvis-react.prod.com/public/version.txt');
        } elseif (App::environment(STG)) {
            $ret = app(FpfHttpClient::class)->get('https://amodvis-react.tester.com/public/version.txt');
        } elseif (App::environment(LOCAL)) {
            $ret = app(FpfHttpClient::class)->get('https://amodvis-react.local.com/public/version.txt');
        } else {
            $ret = getOriginEnv('FRONT_VERSION');
        }
        $ret = trim($ret);
        if (is_numeric($ret)) {
            return $ret;
        }
        return date('YmdH', time());
    }

    public function getListByModuleNames($project_name, $module_names)
    {
        return app(ModuleBasicDao::class)->getListByModuleNames($project_name, $module_names);
    }

    public function getModuleBasicByProjectName($project_name, $page_options)
    {
        return app(ModuleBasicDao::class)->getList($project_name, $page_options);
    }

    public function getModuleInfoByCache($project_name, $module_name)
    {
        return app(ModuleBasicDao::class)->getModuleInfoByCache($project_name, $module_name);
    }

    public function geDataByModuleRowIdArr($module_row_id_arr)
    {
        $module_data_dao = new ModuleDataDao();
        return $module_data_dao->getListByModuleRowIdArr($module_row_id_arr);
    }

    public function getProjectAllPagesInfo($user_id, $app_name, $page_name = '')
    {
        $module_options['project_name'] = 'sys_admin';
        $module_options['module_name'] = 'sys_admin_page_layout';
        if ($page_name) {
            $module_options['page_name'] = $page_name;
        }
        $page_options['page'] = 1;
        // 如果项目页面多得异步获取布局并缓存至浏览器，请一个项目控制在少量的页面
        $page_options['page_size'] = 50;
        $ret = app(AppPageService::class)->getList($user_id, $app_name, $page_options);
        $module_info = [];
        foreach ($ret['data'] as $row) {
            $path = '/' . str_replace('-', '/', $row->page_name);
            if (empty($row->layout)) {
                $tem = [];
            } else {
                $tem = json_decode($row->layout, true);
            }
            foreach ($tem as $module_part => &$row_i) {
                if (empty($row_i)) {
                    unset($tem[$module_part]);
                    continue;
                }
                $row_i = json_decode($row_i, true);
            }
            $module_info[$path] = [
                'path' => $path,
                'modules' => $tem,
                'is_pull_update' => !!intval($row->is_pull_update),
                'is_hide_page' => !!intval($row->is_hide_page),
                'is_pre_load' => !!intval($row->is_pre_load),
                'is_cdn_cache' => !!intval(empty($row->is_cdn_cache) ? 0 : 1),
                'is_user_auth' => property_exists($row, 'is_user_auth') ? $row->is_user_auth : 1,
            ];
        }
        return $module_info;
    }

    public function getUniqueModuleListByModule($user_id, $app_name, $module_options, $page_options)
    {
        $module_unique_dao = new ModuleUniqueDao();
        return $module_unique_dao->getListByModule($user_id, $app_name, $module_options, $page_options);
    }

    public function removeOneProduct($user_id, $module_row_id, $trigger_name, $product_id)
    {
        $module_product_dao = new ModuleProductDao();
        return $module_product_dao->removeProduct($user_id, $module_row_id, $trigger_name, $product_id);
    }

    public function getOneModuleProduct($module_row_id, $product_id)
    {
        $module_product_dao = new ModuleProductDao();
        return $module_product_dao->getOneProduct($module_row_id, $product_id);
    }

    public function saveOneModuleProduct($module_row_id, $product_id, $trigger_name, $data)
    {
        $module_product_dao = new ModuleProductDao();
        return $module_product_dao->saveOneProduct($module_row_id, $product_id, $trigger_name, $data);
    }

    public function chooseOneProduce($user_id, $module_row_id, $trigger_name, $product_id)
    {
        $module_product_dao = new ModuleProductDao();
        return $module_product_dao->save($user_id, $module_row_id, $trigger_name, $product_id);
    }

    public function saveModule($module_row_id, $post_data)
    {
        $module_data_dao = new ModuleDataDao();
        return $module_data_dao->save($module_row_id, $post_data);
    }

    public function getModuleData($module_row_id)
    {
        $module_data_dao = new ModuleDataDao();
        return $module_data_dao->getOne($module_row_id);
    }

    public function getModuleRowId($user_id, $app_name, $project_name, $module_name, $page_name, $position)
    {
        $module_unique_dao = new ModuleUniqueDao();
        $module_options['project_name'] = $project_name;
        $module_options['module_name'] = $module_name;
        $module_options['page_name'] = $page_name;
        $module_options['position'] = $position;
        return $module_unique_dao->getRowId($user_id, $app_name, $module_options);
    }

    public function moduleProductByPage($module_row_id, $page, $page_size, $order = [])
    {
        if ($page_size > ModuleConst::MAX_PAGE_SIZE) {
            $page_size = ModuleConst::MAX_PAGE_SIZE;
        }
        $module_product_dao = new ModuleProductDao();
        $ret = $module_product_dao->getForPage($module_row_id, [], $order, $page, $page_size);
        $ret_row = [];
        $product_id_2_id = [];
        $ret_extend = [];
        $product_trigger_mapping = [];
        foreach ($ret['data'] as $row) {
            $ret_row[$row->product_id] = $row->product_id;
            $product_id_2_id[$row->product_id] = $row->id;
            if (!isset($product_trigger_mapping[$row->trigger_name])) {
                $product_trigger_mapping[$row->trigger_name] = [];
            }
            if ($row->trigger_name) {
                $product_trigger_mapping[$row->trigger_name][] = $row->product_id;
            }
            $ret_extend[$row->product_id] = $row->extend ? json_decode($row->extend, true) : [];
        }
        $goods_service = app(GoodsService::class);
        $ret_items = [];
        if (!empty($ret_row)) {
            $ret_row = $goods_service->queryByIds(array_values($ret_row));
            $ret_row = $goods_service->getMappingList($ret_row);
            foreach ($ret['data'] as $row) {
                if (empty($ret_row[$row->product_id])) {
                    continue;
                }
                $tem_ret = $ret_row[$row->product_id];
                $tem_ret['id'] = $row->id;
                $tem_ret['extend'] = [];
                if ($row->extend) {
                    $extend = $row->extend ? json_decode($row->extend, true) : [];
                    foreach ($extend as $key => $extend_row) {
                        if (isset($tem_ret[$key])) {
                            $tem_ret[$key] = $extend_row;
                        }
                    }
                    $tem_ret['extend'] = $extend;
                }
                $ret_items[] = $tem_ret;
            }
        }
        return [
            'total' => $ret['total'],
            'data' => $ret_items,
            'product_trigger_mapping' => $product_trigger_mapping
        ];
    }

    public function moduleProductCount($module_row_id)
    {
        $module_product_dao = new ModuleProductDao();
        return $module_product_dao->getCount($module_row_id);
    }

    public function moduleProductByProductIds($module_row_id, $product_ids)
    {
        $module_product_dao = new ModuleProductDao();
        $page = 1;
        $page_size = 100;
        $ret = $module_product_dao->getForPage($module_row_id, $product_ids, [], $page, $page_size);
        return $ret['data'] ?? [];
    }

    function orderModuleItems($user_id, $type, $id, $order_type)
    {
        switch ($type) {
            case 'item':
                $table = 'amod_module_product';
                break;
            default:
                return false;
        }
        $model = DB::table($table);
        // check 用户合法性
        $model->where('id', '=', $id);
        $row = (array)$model->first();
        if (!$row) {
            return app('app.response')->arrFail('无效的参数:id');
        }
        if (!HttpAuth::checkUserAllow($user_id, $row['user_id'])) {
            return app('app.response')->arrFail('身份验证:不通过');
        }
        $order_num = $row['order_num'];
        $module_row_id = $row['module_row_id'];
        $trigger = $row['trigger_name'];
        $model = DB::table($table);
        // ToTop ToBottom ToPrev ToNext
        if ($order_type === 'ToTop') {
            $model->selectRaw('min(`order_num`) as ret_id');
        } elseif ($order_type === 'ToBottom') {
            $model->selectRaw('max(`order_num`) as ret_id');
        } elseif ($order_type === 'ToPrev') {
            $model->selectRaw('order_num as ret_id');
        } elseif ($order_type === 'ToNext') {
            $model->selectRaw('order_num as ret_id');
        }
        $model->where('module_row_id', '=', $module_row_id);
        $model->where('trigger_name', '=', $trigger);
        if ($order_type === 'ToPrev') {
            $model->orderByDesc('order_num');
            $model->where('order_num', '<=', intval($order_num));
            $model->where('id', '!=', intval($id));
        } elseif ($order_type === 'ToNext') {
            $model->orderBy('order_num');
            $model->where('order_num', '>=', intval($order_num));
            $model->where('id', '!=', intval($id));
        }
        $row = (array)$model->first();
        if (!$row) {
            return app('app.response')->arrFail('无意义的设置');
        }
        $ret_id = 0;
        if ($row['ret_id']) {
            $ret_id = $row['ret_id'];
        }
        if ($order_type === 'ToTop') {
            $ret_id--;
        } elseif ($order_type === 'ToBottom') {
            $ret_id++;
        } elseif ($order_type === 'ToPrev') {
            $ret_id--;
        } elseif ($order_type === 'ToNext') {
            $ret_id++;
        }
        $model = DB::table($table);
        $ret = $model->where('id', '=', $id)->update(['order_num' => $ret_id]);
        if ($ret < 1) {
            return app('app.response')->arrFail('更新失败');
        }
        // 如果是 移上 小于当前ORDER的项 order_num都减1  如果是 移下 大于当前ORDER的 项 order_num都加1
        $model = DB::table($table);
        $model->where('module_row_id', '=', $module_row_id);
        $model->where('trigger_name', '=', $trigger);
        if ('ToPrev' === $order_type || 'ToNext' === $order_type) {
            $add_string = '';
            if ($order_type === 'ToPrev') {
                $compair_string = '<=';
                $add_string = '-';
            } elseif ($order_type === 'ToNext') {
                $compair_string = '>=';
                $add_string = '+';
            }
            $model->where('order_num', $compair_string, $ret_id);
            $model->where('id', '!=', $id);
            $affect_count = 0;
            if ('-' === $add_string) {
                $affect_count = $model->decrement('order_num');
            } elseif ('+' === $add_string) {
                $affect_count = $model->increment('order_num');
            }
            if (!$affect_count) {
                return app('app.response')->arrSuccess('更新失败');
            }
        }
        return app('app.response')->arrSuccess('更新成功');
    }
}
