<?php

namespace App\Classes\Services\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use ES\Log\FluentLogger;

class UploadService
{

    const PATH_REMOTE_IMAGE_V1 = 'amod_v1';
    const PATH_REMOTE = [
        self::PATH_REMOTE_IMAGE_V1,
    ];

    const FILE_NAME_TYPE_RANDOM = 'random';
    const FILE_NAME_TYPE_ORIGIN = 'origin';

    public function uploadFile($local_file, $remote_path, $name_type = self::FILE_NAME_TYPE_RANDOM)
    {
        $response = app('app.response');
        try {
            $store_service = Storage::disk('s3');
            if (!in_array($remote_path, self::PATH_REMOTE)) {
                return $response->arrFail(trans('common.upload_path_error'));
            }
            if (self::FILE_NAME_TYPE_ORIGIN === $name_type) {
                $base_name = basename($local_file);
            } else {
                $base_name = md5(microtime(true));
                $ext = $this->getFileExt(basename($local_file));
                $base_name .= '.' . $ext;
            }
            $options = [];
            $result = $store_service->putFileAs($remote_path, new File($local_file), $base_name, $options);
            if (empty($result)) {
                return $response->arrFail(trans('common.upload_fail'));
            }
        } catch (\Exception $e) {
            FluentLogger::error('flieupload', ['message' => $e->getMessage(), 'file' => $e->getFile(),
                'line' => $e->getLine()]);
            return $response->arrFail(trans('common.upload_fail'));
        }
        $path = $remote_path . '/' . $base_name;
        $ret = ['url' => $this->getSrcUrl($path, 100), 'path' => $path];
        return $response->arrSuccess($ret);
    }

    private function getFileExt($base_name)
    {
        $temp_arr = explode(".", $base_name);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        return strtolower($file_ext);
    }

    public function getSrcUrl($path, $quality = 70)
    {
        if (empty($path)) {
            return '';
        }
        $secret = config('common.aws_thumb.secret');
        $url = config('common.aws_thumb.url');
        $filters = 'filters:quality(' . $quality . ')';
        $path = "{$filters}/{$path}";
        $data = hash_hmac('sha1', $path, $secret, true);
        $signature = rtrim(strtr(base64_encode($data), '+/', '-_'));
        $full_url = "{$url}{$signature}/{$path}";
        return $full_url;
    }

    /**
     * 获取缩略图URL
     *
     * @param string $path 图片s3路径
     * @param int $width 宽
     * @param int $height 高
     * $path = '310x0/x0/bvjzPct.jpg'
     * '
     * $path = '620x210/10/bvjzPct.jpg'
     * '
     * $path = '0x0/x0/bvjzPct.jpg'
     * '
     * $path = 'filters:quality(10)/0)/bvjzPct.jpg';
     * ';
     *
     * @return string
     */
    function getThumbnailUrl($path, $width, $height = 0)
    {
        if (empty($path)) {
            return ''; //不做图片显示
        }
        $secret = config('gen.thumb.secret');
        $url = config('gen.thumb.url');
        $path = "{$width}x{$height}/{$path}";
        $data = hash_hmac('sha1', $path, $secret, true);
        $signature = rtrim(strtr(base64_encode($data), '+/', '-_'));
        $full_url = "{$url}/{$signature}/{$path}";
        return $full_url;
    }
}
