<?php

namespace App\Classes\Services\Goods;

use App\Classes\Utils\HttpAuth;
use ES\Net\Http\FpfHttpClient;

use Cache;

class GoodsService
{
    public function getMappingList($product_list)
    {
        $tem = [];
        foreach ($product_list as $item) {
            $tem[$item['item_id']] = $item;
        }
        return $tem;
    }

    public function queryByIds($id_arr)
    {
        sort($id_arr);
        $page = 1;
        $page_size = 100;
        $filter = [
            'product_id' => implode(',', $id_arr)
        ];
        $rows_json = Cache::get('product_list-' . implode(":", $id_arr));
        if ($rows_json && !empty($rows['data'])) {
            $rows = json_decode($rows_json, true);
        } else {
            $rows = $this->getItems($filter, $page, $page_size);
            if (!empty($rows['data'])) {
                Cache::put('product_list-' . implode(":", $id_arr), json_encode($rows), 60);
            }
        }
        return $rows['total'] ? $rows['data'] : [];
    }

    public function getDetail($product_id, $app_key = '', $w = 375, $h = 375)
    {
        $authorization = HttpAuth::getEcOpenAuthorization();
        $http_client = app(FpfHttpClient::class);
        $api = getOriginEnv('EC_APP_INNER_BASE_URL') . 'api/1.0/open/goods/detail';
        $get_arr = ['product_id' => $product_id, 'w' => $w, 'h' => $h];
        $http_option = [
            'headers' => [
                'Authorization' => 'Bearer ' . $authorization,
                'appkey' => $app_key,
            ]
        ];
        $ret = $http_client->get($api, $get_arr, $http_option);
        $ret = json_decode($ret, true);
        if (empty($ret) || false == $ret['status']) {
            return [];
        }
        return $ret['data'];
    }

    public function getItems($filter, $page, $page_size)
    {
        $authorization = HttpAuth::getEcOpenAuthorization();
        $http_client = app(FpfHttpClient::class);
        $api = getOriginEnv('EC_APP_INNER_BASE_URL') . 'api/goods/list/all';
        $get_arr = $filter;
        $get_arr['page'] = $page;
        $get_arr['w'] = request()->input('w', 300);
        $get_arr['h'] = request()->input('h', 300);
        $get_arr['page_size'] = $page_size;
        $http_option = [
            'headers' => ['Authorization' => 'Bearer ' . $authorization]
        ];
        $ret = $http_client->get($api, $get_arr, $http_option);
        $ret = json_decode($ret, true);
        if (empty($ret) || false == $ret['status']) {
            return [
                'total' => 0,
                'data' => [],
            ];
        }
        $rows = $ret['data']['data'] ?? [];
        $ret_items = [];
        foreach ($rows as $row) {
            $ret_item = [
                'item_id' => $row['id'],
                'id' => $row['id'],
                'img' => $row['thumb'] ?? '',
                'store_name' => $row['store_name'] ?? '',
                'title' => $row['name']
            ];
            if (isset($row['mix_point']) || isset($row['mix_price'])) {
                $ret_item['point'] = $row['mix_point'];
                $ret_item['price'] = $row['mix_price'];
                $ret_item['ori_price'] = $ret_item['price'];
            } elseif (isset($row['point'])) {
                $ret_item['point'] = $row['point'];
                $ret_item['price'] = 0;
                $ret_item['ori_price'] = 0;
            } elseif (isset($row['price']) || isset($row['ori_price'])) {
                $ret_item['price'] = $row['price'];
                $ret_item['ori_price'] = $row['ori_price'];
                $ret_item['point'] = 0;
            }
            $ret_item['show_pay_type'] = $row['show_pay_type'] ?? 0;
            $ret_items[$ret_item['item_id']] = $ret_item;
        }
        $ret = [
            'total' => $ret['data']['total'] ?? 0,
            'data' => array_values($ret_items),
        ];
        return $ret;
    }
}
