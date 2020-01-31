<?php

namespace App\Classes\Services\Vendor;


class VendorService
{
    public function getVendorIdIdByVendorToken($amodvis_vendor_token)
    {
        if (!$amodvis_vendor_token) {
            return 0;
        }
    }


    public function getVendorList($user_id)
    {
        $is_admin = false;
        if (in_array($user_id, config('common.super_admin_user_id_list'))) {
            $is_admin = true;
        }
        $vendor_list = [];
        if (true === $is_admin) {
            $vendor_list = config('common.important_vendor_list_for_item_query');
        }
        if (empty($vendor_list)) {
            return [];
        }
        return $this->queryVendorByIds($vendor_list);
    }

    public function queryVendorByIds($vendor_list)
    {
        return [
            ['id' => 1, 'name' => 'vendor1'],
            ['id' => 7, 'name' => 'vendor7'],
            ['id' => 52, 'name' => 'vendor52'],
        ];
    }

    public function getShopListByVendorId($vendor_id)
    {
        $shop_list = [
            1 => [
                ['shop_name' => 'shop1-1', 'vendor_id' => 1, 'shop_id' => 10001],
                ['shop_name' => 'shop1-2', 'vendor_id' => 1, 'shop_id' => 10002],
                ['shop_name' => 'shop1-3', 'vendor_id' => 1, 'shop_id' => 10003],
                ['shop_name' => 'shop1-4', 'vendor_id' => 1, 'shop_id' => 10004],
                ['shop_name' => 'shop1-5', 'vendor_id' => 1, 'shop_id' => 10005],
            ],
            7 => [
                ['shop_name' => 'shop7-1', 'vendor_id' => 7, 'shop_id' => 70001],
                ['shop_name' => 'shop7-2', 'vendor_id' => 7, 'shop_id' => 70002],
                ['shop_name' => 'shop7-3', 'vendor_id' => 7, 'shop_id' => 70003],
            ],
            52 => [
                ['shop_name' => 'shop52-1', 'vendor_id' => 52, 'shop_id' => 520001],
                ['shop_name' => 'shop52-2', 'vendor_id' => 52, 'shop_id' => 520002],
                ['shop_name' => 'shop52-3', 'vendor_id' => 52, 'shop_id' => 520003],
                ['shop_name' => 'shop52-4', 'vendor_id' => 52, 'shop_id' => 520004],
            ]
        ];
        return $shop_list[$vendor_id] ?? [];
    }
}
