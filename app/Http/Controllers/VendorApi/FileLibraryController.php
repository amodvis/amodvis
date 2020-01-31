<?php
/**
 * 文件库 素材库
 */

namespace App\Http\Controllers\VendorApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Classes\Services\Media\MediaService;

class FileLibraryController extends Controller
{
    public function getUploadZoneFileList(Request $request)
    {
        $vendor_id = $request->input('login_vendor_id');
        $cate_id = $request->input('cate_id');
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 20);
        if ($page_size > 50) {
            $page_size = 50;
        }
        $page_options = [
            'page' => $page,
            'page_size' => $page_size,
        ];
        $data = app(MediaService::class)->getItemsByPathId($vendor_id, $cate_id, $page_options);
        return app('app.response')->jsonSuccess($data);
    }

    public function addOneFile(Request $request)
    {
        $with = $request->input('with', 0);
        $height = $request->input('height', 0);
        $name = $request->input('name', '');
        $url = $request->input('url', '');
        $size = $request->input('size', 0);
        $path_id = $request->input('path_id', 0);
        $vendor_id = $request->input('login_vendor_id');
        $item = pathinfo($url);
        $ext = $item['extension'] ?? '';
        if (in_array($ext, ['gif', 'jpg', 'jpeg', 'png', 'svg'])) {
            $file_type = MediaService::FILE_TYPE_IMAGE;
        } else if (in_array($ext, ['swf', 'flv', 'rm', 'rmvb', 'mpg', 'avi', 'wmv'])) {
            $file_type = MediaService::FILE_TYPE_VIDEO;
        } else if (in_array($ext, ['mp3', 'wma', 'wav', 'mid'])) {
            $file_type = MediaService::FILE_TYPE_AUDIO;
        } else {
            $file_type = MediaService::FILE_TYPE_OTHER;
        }
        $data = [
            'width' => intval($with),
            'height' => intval($height),
            'nick_name' => $name,
            'file_name' => $url,
            'file_type' => $file_type,
            'path_id' => $path_id,
            'size' => $size
        ];
        $data = app(MediaService::class)->addOneFile($vendor_id, $data);
        return app('app.response')->jsonSuccess($data);
    }

    public function addOnePath(Request $request)
    {
        $path_name = $request->input('path_name', '');
        $vendor_id = $request->input('login_vendor_id');
        $path_id = app(MediaService::class)->addOnePath($vendor_id, $path_name);
        return app('app.response')->jsonSuccess(['id' => $path_id, 'path_name' => $path_name]);
    }

    public function renameFile(Request $request)
    {
        $new_name = trim($request->input('new_name', ''));
        $id = $request->input('id', '');
        $vendor_id = $request->input('login_vendor_id');
        if (!$new_name || !$id) {
            return app('app.response')->jsonError('參數錯誤');
        }
        try {
            $ret = app(MediaService::class)->renameFile($vendor_id, $id, $new_name);
        } catch (\Illuminate\Database\QueryException $e) {
            return app('app.response')->jsonError('同目錄文件名不允許重複');
        }
        if ($ret) {
            return app('app.response')->jsonSuccess('操作成功');
        }
        return app('app.response')->jsonError('操作失敗');
    }

    public function delFile(Request $request)
    {
        $id = $request->input('id', '');
        $vendor_id = $request->input('login_vendor_id');
        if (!$id) {
            return app('app.response')->jsonError('參數錯誤');
        }
        $ret = app(MediaService::class)->delFile($vendor_id, $id);
        if ($ret) {
            return app('app.response')->jsonSuccess('操作成功');
        }
        return app('app.response')->jsonError('操作失敗');
    }
}
