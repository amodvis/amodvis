<?php

namespace App\Classes\Models\Amod;

use Illuminate\Support\Facades\DB;

class ModuleProductDao
{
    public function save($user_id, $module_row_id, $trigger_name, $product_id, $extend = [])
    {
        $model = DB::table('amod_module_product');
        $extend_db = '';
        if (!empty($extend)) {
            $extend_db = json_encode($extend);
        }
        return $model->insert([
            'user_id' => $user_id,
            'module_row_id' => $module_row_id,
            'product_id' => $product_id,
            'trigger_name' => $trigger_name,
            'extend' => $extend_db
        ]);
    }

    public function getOneProduct($module_row_id, $product_id)
    {
        $model = DB::table('amod_module_product');
        return $model->where('module_row_id', '=', $module_row_id)
            ->where('product_id', '=', $product_id)
            ->first();
    }

    public function saveOneProduct($module_row_id, $product_id, $trigger_name, $data)
    {
        $model = DB::table('amod_module_product');
        return $model->where('module_row_id', '=', $module_row_id)
            ->where('product_id', '=', $product_id)
            ->where('trigger_name', '=', $trigger_name)
            ->update(['extend' => json_encode($data)]);
    }

    public function removeProduct($user_id, $module_row_id, $trigger_name, $product_id)
    {
        $model = DB::table('amod_module_product');
        return $model->where('user_id', '=', $user_id)
            ->where('module_row_id', '=', $module_row_id)
            ->where('trigger_name', '=', $trigger_name)
            ->where('product_id', '=', $product_id)
            ->delete();
    }

    public function getForPage($module_row_id, $product_ids, $order, $page, $page_size)
    {
        $model = DB::table('amod_module_product');
        if (is_array($product_ids) && !empty($product_ids)) {
            $model->whereIn('product_id', $product_ids);
        }
        $obj = $model->where('module_row_id', '=', $module_row_id);
        $count = $obj->count();
        $obj->forPage($page, $page_size);
        if (!empty($order)) {
            foreach ($order as $order_row) {
                $obj->orderBy($order_row[0], $order_row[1]);
            }
        }
        $items = $obj->get()->toArray();
        return ['data' => $items, 'total' => $count];
    }

    public function getCount($module_row_id)
    {
        $model = DB::table('amod_module_product');
        return $model->where('module_row_id', '=', $module_row_id)->count();
    }
}
