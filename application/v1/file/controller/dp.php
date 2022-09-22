<?php

namespace app\v1\file\controller;


use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use OSS\Core\OssException;
use SendFile\SendFile;

class dp extends CommonController
{

    public $token;

    public function initialize()
    {
        set_time_limit(0);
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
    }

    public function index()
    {
        dump(config('aliyun.'));
    }

    public function upload($dir = '', $from = '', $module = '')
    {
        // 临时取消执行时间限制
        set_time_limit(0);

        if ($from == 'ueditor') {
            return $this->ueditor();
        }

        if ($from == 'jcrop') {
            return $this->jcrop();
        }

        return $this->saveFile($dir, $from, $module);
    }

    private function saveFile($dir = '', $from = '', $module = '')
    {

        set_time_limit(0);
        $token = $this->token;
        $proc = ProjectModel::api_find_token($token);
        if (!$proc) {
            return $this->uploadError($from, "项目不可用");
        }


        // 缩略图参数
        $thumb = $this->request->post('thumb', '');
        // 水印参数
        $watermark = $this->request->post('watermark', '');

        // 获取附件数据
        $callback = '';
        switch ($from) {
            case 'editormd':
                $file_input_name = 'editormd-image-file';
                break;
            case 'ckeditor':
                $file_input_name = 'upload';
                $callback = $this->request->get('CKEditorFuncNum');
                break;
            case 'ueditor_scrawl':
                return $this->saveScrawl();
                break;
            default:
                $file_input_name = 'file';
        }
        $file = $this->request->file($file_input_name);
        $file_name = $file->getInfo('name');
        if (!$file) {
            return $this->uploadError($from, "请先上传文件", $callback);
        }
        $md5 = $file->hash('md5');
        $sha1 = $file->hash("sha1");
        $mime = $file->getInfo('type');
        // 判断附件格式是否符合

        if ($file_info = AttachmentModel::get(['token' => $token, 'md5' => $md5])) {
            $sav = $proc['url'] . '/' . $file_info['path'];
            return $this->uploadSuccess($from, $sav, $file_info['name'], $sav, $callback, $file_info);
        }

        if ($file->getMime() == 'text/x-php' || $file->getMime() == 'text/html') {
            return $this->uploadError($from, "禁止上传非法文件", $callback);
        }
        $info = $file->validate(['size' => (float)$proc['size'] * 1024, 'ext' => $proc['ext']])->move('./upload/' . $this->token);
        if (!$info) {
            return $this->uploadError($from, "上传不符合规范", $callback);
        }

        $fileName = $proc['name'] . '/' . $info->getSaveName();
        $fileName = str_replace("\\", "/", $fileName);


        $duration = 0;
        $duration_str = "00:00";
        $bitrate = 0;
        $width = 0;
        $height = 0;

        $ext = $info->getExtension();

        switch ($ext) {
            case "mp3":
            case "wav":
            case "ogg":
            case "asf":
            case "wmv":
            case "avi":
            case "mp4":
            case "aac":
                $getId3 = new \getID3();
            $ana = $getId3->analyze($info->getPathname());
            $duration = $ana["playtime_seconds"];
            $bitrate = $ana["bitrate"];
            $duration_str = $ana["playtime_string"];
            $width = $ana["resolution_x"];
            $height = $ana["resolution_y"];
            break;

            case "png":
            case "jpg":
            case "jpeg":
            case "bmp":
            case "gif":
            case "tiff":
            $getId3 = new \getID3();
            $ana = $getId3->analyze($info->getPathname());
            $width = $ana["resolution_x"];
            $height = $ana["resolution_y"];
            $bitrate = $ana["bits_per_sample"];
            $duration_str = $ana["compression_ratio"];
            break;
        }

        $file_info = [
            'token' => $token,
            'name' => $file_name,
            'mime' => $mime,
            'path' => $fileName,
            'ext' => $ext,
            'size' => $info->getSize(),
            'md5' => $md5,
            'sha1' => $sha1,
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'duration_str' => $duration_str,
            'bitrate' => $bitrate,
        ];

        if ($proc["type"] == "local" || $proc["type"] == "all") {
            if ($proc['main_type'] == 'local') {
                $sav = $proc['url'] . '/' . $fileName;
            }
        }
        if ($proc["type"] == "dp" || $proc["type"] == "all") {
            $sf = new SendFile();
            $ret = $sf->send('http://' . $proc["endpoint"] . '/up?token=' . $proc["bucket"], realpath('./upload/' . $fileName), $file->getInfo('type'), $file->getInfo('name'));
            $json = json_decode($ret, 1);
            $sav = $proc['url'] . '/' . $json["data"];
        }
        if ($proc["type"] == "oss" || $proc["type"] == "all") {
            try {
                $oss = new \OSS\AliyunOSS($proc);
                $ret = $oss->uploadFile($proc['bucket'], $fileName, $info->getPathname());
            } catch (OssException $e) {
                \Ret::fail($e->getMessage(), 200);
            }
            if (empty($ret->getData()["info"]["url"])) {
                \Ret::fail("OSS不正常");
            }
            if ($proc['main_type'] == 'oss') {
                $sav = $proc['url'] . '/' . $fileName;
            }
            if ($proc["type"] != "all") {
                unlink($info->getPathname());
            }
        }

        // 写入数据库
        if ($file_add = AttachmentModel::create($file_info)) {
            return $this->uploadSuccess($from, $sav, $file_info['name'], $sav, $callback, $file_info);
        } else {
            return $this->uploadError($from, '上传失败', $callback);
        }
    }

    private function uploadError($from, $msg = '', $callback = '')
    {
        switch ($from) {
            case 'wangeditor':
                return "error|" . $msg;
                break;
            case 'ueditor':
                return json(['state' => $msg]);
                break;
            case 'editormd':
                return json(["success" => 0, "message" => $msg]);
                break;
            case 'ckeditor':
                return ck_js($callback, '', $msg);
                break;
            default:
                return json([
                    'code' => 0,
                    'class' => 'danger',
                    'info' => $msg,
                ]);
        }
    }

    private function uploadSuccess($from, $file_path = '', $file_name = '', $file_id = '', $callback = '', $data = [])
    {
        switch ($from) {
            case 'wangeditor':
                return $file_path;
                break;
            case 'ueditor':
                return json([
                    "state" => "SUCCESS", // 上传状态，上传成功时必须返回"SUCCESS"
                    "url" => $file_path, // 返回的地址
                    "title" => $file_name, // 附件名
                    "data" => $data,
                ]);
                break;
            case 'editormd':
                return json([
                    "success" => 1,
                    "message" => '上传成功',
                    "url" => $file_path,
                    "data" => $data,
                ]);
                break;
            case 'ckeditor':
                return ck_js($callback, $file_path);
                break;
            default:
                return json([
                    'code' => 1,
                    'info' => '上传成功',
                    'class' => 'success',
                    'id' => $file_path,
                    'path' => $file_path,
                    "data" => $data,
                ]);
        }
    }

    public function upload_ueditor()
    {
        $action = $this->request->get('action');
        $config_file = './static/libs/ueditor/php/config.json';
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config_file)), true);
        switch ($action) {
            /* 获取配置信息 */
            case 'config':
                $result = $config;
                break;

            /* 上传图片 */
            case 'uploadimage':
                return $this->saveFile('images', 'ueditor');
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                return $this->saveFile('images', 'ueditor_scrawl');
                break;

            /* 上传视频 */
            case 'uploadvideo':
                return $this->saveFile('videos', 'ueditor');
                break;

            /* 上传附件 */
            case 'uploadfile':
                return $this->saveFile('files', 'ueditor');
                break;

            /* 列出图片 */
            case 'listimage':
                return $this->showFile('listimage', $config);
                break;

            /* 列出附件 */
            case 'listfile':
                return $this->showFile('listfile', $config);
                break;

            /* 抓取远程附件 */
//            case 'catchimage':
            //                $result = include("action_crawler.php");
            //                break;

            default:
                $result = ['state' => '请求地址出错'];
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                return htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                return json(['state' => 'callback参数不合法']);
            }
        } else {
            return json($result);
        }
    }

}
