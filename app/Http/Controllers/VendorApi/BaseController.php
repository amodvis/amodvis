<?php
/**
 * 商家,店铺,门店基础信息
 */

namespace App\Http\Controllers\VendorApi;

use App\Http\Controllers\Controller;
use \App\Classes\Services\Vendor\VendorService;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function getShopByVendorId(Request $request, $vendor_id)
    {
        $shop_list = app(VendorService::class)->getShopListByVendorId($vendor_id);
        return app('app.response')->jsonSuccess($shop_list);
    }
}
