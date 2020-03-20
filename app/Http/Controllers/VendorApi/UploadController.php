<?php
/**
 * 文件上传
 */

namespace App\Http\Controllers\VendorApi;

use App\Http\Controllers\Controller;
use App\Classes\Services\Media\UploadService;
use ES\Log\FluentLogger;

class UploadController extends Controller
{
    public $staticPath;
    public $commonFilePath;
    public $uploadFrom;
    const STATIC_PATH_FOR_COMBINE = 'amodvis/static/';

    const FILE_LIST = [
        'image' => ['gif', 'jpg', 'jpeg', 'png', 'svg'],
        'flash' => ['swf', 'flv'],
        'media' => ['swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
        'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
    ];

    public function __construct()
    {
        $basePath = getOriginEnv('UPLOAD_FILE_PATH');
        $this->staticPath = $basePath . self::STATIC_PATH_FOR_COMBINE;
        $this->commonFilePath = $basePath . self::STATIC_PATH_FOR_COMBINE;
    }

    private function getFileExt()
    {
        $file_name = $_FILES['fileUploadName']['name'];
        $temp_arr = explode(".", $file_name);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        return strtolower($file_ext);
    }

    private function getFileGroup()
    {
        $ext = $this->getFileExt();
        $file_list = self::FILE_LIST;
        $group = '';
        foreach ($file_list as $group_name => $item) {
            if (in_array($ext, $item)) {
                $group = $group_name;
            }
        }
        return $group;
    }

    public function checkUpload($dir_name)
    {

        //定义允许上传的文件扩展名
        $ext_arr = self::FILE_LIST;
        //最大文件大小
        $max_size = 20971520; // 20M
        if (empty($_FILES['fileUploadName'])) {
            return app('app.response')->arrFail($this->alert("文件上传异常"));
        }
        //PHP上传失败
        if (!empty($_FILES['fileUploadName']['error'])) {
            switch ($_FILES['fileUploadName']['error']) {
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            return app('app.response')->arrFail($this->alert($error));
        }

        //原文件名
        $file_name = $_FILES['fileUploadName']['name'];
        //服务器上临时文件名
        $tmp_path_file = $_FILES['fileUploadName']['tmp_name'];
        //文件大小
        $file_size = $_FILES['fileUploadName']['size'];
        //检查文件名
        if (!$file_name) {
            return app('app.response')->arrFail($this->alert("请选择文件。"));
        }
        //获得文件扩展名
        $file_group = $this->getFileGroup();
        $file_ext = $this->getFileExt();
        $file_real_path = $this->commonFilePath . $file_group . '/';
        //检查目录
        if (is_dir($file_real_path) === false) {
            mkdir($file_real_path, 0755, true);
        }
        //检查目录写权限
        if (is_writable($file_real_path) === false) {
            return app('app.response')->arrFail($this->alert("上传目录没有写权限。"));
        }
        //检查是否已上传
        if (is_uploaded_file($tmp_path_file) === false) {
            return app('app.response')->arrFail($this->alert("上传失败。"));
        }

        //检查文件大小
        if ($file_size > $max_size) {
            return app('app.response')->arrFail($this->alert("上传文件大小超过限制。"));
        }
        //检查目录名
        if (empty($ext_arr[$dir_name])) {
            return app('app.response')->arrFail($this->alert("目录名不正确。"));
        }
        //检查扩展名
        if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
            return app('app.response')->arrFail("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
        }
        $save_path = $this->staticPath;
        $save_url = getOriginEnv('AMOD_FRONT_UPLOAD_BASE_URL');
        //创建文件夹
        if ($dir_name !== '') {
            $save_path .= $dir_name . "/";
            $save_url .= self::STATIC_PATH_FOR_COMBINE . $dir_name . "/";
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
        }
        // 新文件名
        $error_flag = true;
        $new_file_name = $path_a = $path_b = $path_c = '';
        for ($n = 1; $n < 5; $n++) {
            $ret = $this->getFileName($save_path, $file_ext);
            if ($ret['error'] === false) {
                $msg = $ret['message'];
                list($new_file_name, $path_a, $path_b, $path_c) = $msg;
                $error_flag = false;
                break;
            }
        }
        if ($error_flag === true) {
            return app('app.response')->arrFail($this->alert("上传文件失败。"));
        }
        $path_abc = $path_a . "/" . $path_b . "/" . $path_c . "/";
        $path_4 = $save_path . $path_abc;
        if (!is_dir($path_4)) {
            mkdir($path_4, 0755, true);
        }
        $save_url .= $path_abc . $new_file_name . "." . $file_ext;
        $new_path_file = '/' . $path_abc . $new_file_name . "." . $file_ext;
        //移动文件
        $file_path = $save_path . $new_path_file;
        return app('app.response')->arrSuccess([$save_url, $file_path, $new_file_name, $tmp_path_file, $file_size]);
    }

    function getFileName($save_path, $file_ext)
    {
        $new_file_name = md5(uniqid());
        $path_a = substr($new_file_name, 0, 2);
        $path_b = substr($new_file_name, 2, 2);
        $path_c = substr($new_file_name, 4, 2);
        if (file_exists($save_path . $path_a . "/" . $path_b . "/" . $path_c . "/" . $new_file_name . "." . $file_ext)) {
            return app('app.response')->arrFail('file is not exists!');
        } else {
            return app('app.response')->arrSuccess([$new_file_name, $path_a, $path_b, $path_c]);
        }
    }

    function alert($msg)
    {
        $res = [];
        if ($this->uploadFrom === "kingeditor") {
            $res = array(
                'error' => 1,
                'message' => $msg
            );
        } elseif ($this->uploadFrom === "kissy") {
            $res = array(
                "status" => 0,
                "type" => "ajax",
                "name" => "",
                "url" => '',
                "msg" => $msg,
                "size" => array()
            );
        }
        return response(json_encode($res));
    }

    // kingeditor
    public function uploadeditor()
    {
        $this->uploadFrom = 'kingeditor';
        return $this->baseUploadJson();
    }

    public function uploadkissy()
    {
        $this->uploadFrom = 'kissy';
        return $this->baseUploadJson();
    }

    private function baseUploadJson()
    {
        $type = request()->input('type', 'iframe');
        $dir_name = $this->getFileGroup();
        $script = '';
        if ($type === 'iframe') {
            $script = '<script>document.domain="' . config('common.amod_js_domain') . '";</script>';
        }
        $ret = $this->checkUpload($dir_name);
        if (true === $ret['error']) {
            return $ret['message'];
        }
        list($file_url, $file_path, $new_file_name, $tmp_path_file, $file_size) = $ret['message'];
        $move_flag = false;
        $error_msg = '';
        if (move_uploaded_file($tmp_path_file, $file_path) === false) {
            $move_flag = true;
            $error_msg = "错误1001";
        }
        if ($move_flag === true) {
            return $this->alert($error_msg);
        }
        @chmod($file_path, 0644);
        if ($this->uploadFrom === "kingeditor") {
            $res = array(
                'error' => 0,
                'url' => $file_url
            );
        } elseif ($this->uploadFrom === "kissy") {
            $res = array(
                "status" => 1,
                "type" => "ajax",
                "name" => $new_file_name,
                "url" => $file_url,
                "size" => $file_size,
            );
            if ('image' === $this->getFileGroup()) {
                list($picWidth, $picHeight) = getimagesize($file_path);
                $res['width'] = $picWidth;
                $res['height'] = $picHeight;
            }
        } else {
            return $this->alert('未知的上传组件');
        }
        var_dump($_SERVER);
        $is_to_remote = getOriginEnv('IS_UPLOAD_TO_REMOTE');
        if ($is_to_remote) {
            try {
                $ret = app(UploadService::class)->uploadFile($file_path, UploadService::PATH_REMOTE_IMAGE_V1);
                $url = '';
                $path = '';
                if (false === $ret['error'] && !empty($ret['message'])) {
                    $url = $ret['message']['url'];
                    $path = $ret['message']['path'];
                }
                $res['url'] = $url;
                $res['path'] = $path;
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                FluentLogger::error('common', ['message' => $e->getMessage(), 'file' => $e->getFile(),
                    'line' => $e->getLine()]);
            }
        }
        return response($script . json_encode($res))->withHeaders(['Content-type', 'text/html; charset=UTF-8']);
    }
}
