<?php

namespace App\Classes\Services\Module;

class ModuleDataCheckService
{
    public static function checkModuleData($project_name, $module_name, $data)
    {
        $fun = $project_name . '_' . $module_name;
        if (method_exists(self::class, $fun)) {
            return self::$fun($data);
        }
        return app('app.response')->arrSuccess();
    }

    protected static function sys_admin_sys_admin_page_layout($data)
    {
        foreach ($data as $json) {
            if (!$json) {
                return app('app.response')->arrFail('数据格式错误，请关闭后重新编辑 10001');
            }
            $rows = json_decode($json, true);
            if(empty($rows) && is_array($rows)){
                continue;
            }
            if (!$rows) {
                return app('app.response')->arrFail('数据格式错误，请关闭后重新编辑 10002');
            }
            foreach ($rows as $row) {
                foreach ($row as $hole_type => $json_items) {
                    if (!in_array($hole_type, ['main', 'sub_min', 'sub_max'])) {
                        return app('app.response')->arrFail('数据格式错误，请关闭后重新编辑 10003');
                    }
                    foreach ($json_items as $json_items) {
                        foreach ($json_items as $key => $json_item) {
                            if (!in_array($key, ['project_name', 'module_name', 'page_name', 'position'])) {
                                return app('app.response')->arrFail('数据格式错误，请关闭后重新编辑 10004');
                            }
                        }
                    }
                }
            }
        }
        return app('app.response')->arrSuccess();
    }
}
