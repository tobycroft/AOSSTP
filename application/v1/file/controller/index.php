<?php

namespace app\v1\file\controller;


use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use OSS\AliyunOSS;
use OSS\Core\OssException;
use SendFile\SendFile;
use think\Request;

class index extends search
{

    public function index()
    {
        dump(config('aliyun.'));
    }

    public function upload_file(Request $request, $full = 0, $type = null)
    {
        $token = $this->token;
        $proc = $this->proc;

        $file = $request->file('file');
        if (!$file) {
            \Ret::Fail(400, null, 'file字段没有用文件提交');
        }
        $file_name = $file->getInfo('name');
        $md5 = $file->hash('md5');
        $sha1 = $file->hash("sha1");
        $mime = $file->getInfo('type');
        // 判断附件格式是否符合


        if ($file_exists = AttachmentModel::get(['token' => $token, 'md5' => $md5, 'sha1' => $sha1])) {
            $sav = $this->getStr($full, $proc['url'], $file_exists, $type);
        } elseif ($file_exists = AttachmentModel::get(['token' => $token, 'md5' => $md5])) {
            if (!AttachmentModel::update(["sha1" => $sha1], ['token' => $token, 'md5' => $md5])) {
                \Ret::Fail(500, null, "sha1更新失败");
            }
            $sav = $this->getStr($full, $proc['url'], $file_exists, $type);
        }
        $info = $file->move('./upload/' . $this->token);
        if (!$info) {
            \Ret::Fail(300, null, $file->getError());
            return;
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
                break;

            case "png":
            case "jpg":
            case "jpeg":
            case "bmp":
            case "gif":
            case "tiff":
                $getId3 = new \getID3();
                $ana = $getId3->analyze($info->getPathname());
                $width = $ana["video"]["resolution_x"];
                $height = $ana["video"]["resolution_y"];
                $bitrate = $ana["video"]["bits_per_sample"];
                $duration_str = $ana["video"]["compression_ratio"];
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
                $sav = ($full ? $proc['url'] . '/' : '') . $fileName;
            }
        }
        if ($proc["type"] == "dp" || $proc["type"] == "all") {
            $sf = new SendFile();
            $ret = $sf->send('http://' . $proc["endpoint"] . '/up?token=' . $proc["bucket"], realpath('./upload/' . $fileName), $file->getInfo('type'), $file->getInfo('name'));
            $json = json_decode($ret, 1);
            $sav = ($full ? $proc['url'] . '/' : '') . $json["data"];
        }
        if ($proc["type"] == "oss" || $proc["type"] == "all") {
            try {
                $oss = new AliyunOSS($proc);
                $ret = $oss->uploadFile($proc['bucket'], $fileName, $info->getPathname());
            } catch (OssException $e) {
                \Ret::Fail(200, null, $e->getMessage());
            }
            if (empty($ret->getData()["info"]["url"])) {
                \Ret::Fail(200, null, "OSS不正常");
            }
            if ($proc['main_type'] == 'oss') {
                $sav = ($full ? $proc['url'] . '/' : '') . $fileName;
            }
            if ($proc["type"] != "all") {
                unlink($info->getPathname());
            }
        }

        AttachmentModel::create($file_info);
        if ($info) {
            switch ($type) {
                case "ue":
                    \Ret::Success(0, ['src' => $sav]);
                    break;

                case "complete":
                    $file_info["src"] = $sav;
                    $file_info["url"] = $proc['url'] . '/' . $file_info['path'];
                    $file_info["surl"] = $file_info['path'];
                    \Ret::Success(0, $file_info);
                    break;

                default:
                    \Ret::Success(0, $sav);
                    break;
            }
        } else {
            \Ret::Fail(300, null, $file->getError());
        }
    }

    public function up(Request $request)
    {
        $file = $request->file('file');
        if ($file) {
            $this->upload_file($request);
        } else {
            \Ret::Fail(400, null, "请上传binary文件");
//            $this->upload_base64($request);
        }
    }

    public function upfull(Request $request)
    {
        $file = $request->file('file');
        if ($file) {
            $this->upload_file($request, 1);
        } else {
            \Ret::Fail(400, null, "请上传binary文件");
//            $this->upload_base64($request, 1);
        }
    }

    public function up_ue(Request $request)
    {
        $file = $request->file('file');
        if ($file) {
            $this->upload_file($request, 1, "ue");
        } else {
            \Ret::Fail(400, null, "请上传binary文件");
//            $this->upload_base64($request, 1, 1);
        }
    }

    public function up_complete(Request $request)
    {
        $file = $request->file('file');
        if ($file) {
            $this->upload_file($request, 1, "complete");
        } else {
            \Ret::Fail(400, null, "请上传binary文件");
//            $this->upload_base64($request, 1, 1);
        }
    }

//    public function upload_base64(Request $request, $full = 0, $ue = 0)
//    {
//        $token = $this->token;
//        if (!$request->has('file')) {
//            \Ret::Fail(400,null,'需要file字段提交base64');
//        }
//        $image = input('post.file');
//        if (!$image) {
//            return [
//                'code' => 404,
//                'data' => '没有找到文件'
//            ];
//        }
//        $savePath = date('Ymd', time()) . '/';
//        $file_name = md5(time() . microtime());
//
//        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $image, $result)) {
//            $proc = ProjectModel::api_find_token($token);
//            if (!$proc) {
//                \Ret::Fail(401,null,'项目不可用');
//            }
//            $ext = explode(',', $proc['ext']);
//            $type = $result[2];
//            if (!in_array($type, $ext)) {
//                $_message['message'] = '仅允许:' . $proc['ext'];
//                return $_message;
//            }
//            $pic_path = 'upload/' . $savePath;
//            $file_path = $pic_path . $file_name . "." . $type;
//            if (!file_exists($pic_path)) {
//                mkdir($pic_path);
//            }
//            $file_size = file_put_contents($file_path, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image)));
//            if (!$file_path || $file_size > 10 * 1024 * 1024) {
//                unlink($pic_path);
//                return [
//                    'code' => 500,
//                    'data' => '图片保存失败'
//                ];
//            }
//            $md5 = md5_file($file_path);
//
//            if ($file_exists = AttachmentModel::where(['md5' => $md5])->find()) {
//                $sav = ($full ? $proc['url'] . '/' : '') . $file_exists['path'];
//                // 附件已存在
//                return \Ret::succ($sav);
//            }
//
//            $fileName = $proc['name'] . '/' . $savePath . $file_name . "." . $type;
//
//            if ($proc["type"] == "local" || $proc["type"] == "all") {
//                if ($proc['main_type'] == 'local') {
//                    $sav = ($full ? $proc['url'] . '/' : '') . $fileName;
//                }
//            }
//            if ($proc["type"] == "oss" || $proc["type"] == "all") {
//                $oss = new AliyunOSS($proc);
//                $oss->uploadFile($proc['bucket'], $fileName, $file_path);
//                if ($proc['main_type'] == 'oss') {
//                    $sav = ($full ? $proc['url'] . '/' : '') . $fileName;
//                }
//                unlink($file_path);
//            }
//
//            $file_info = [
//                'token' => $token,
//                'name' => $file_name,
//                'mime' => $type,
//                'path' => $fileName,
//                'ext' => $ext,
//                'size' => $file_size,
//                'md5' => $md5,
//            ];
//            AttachmentModel::create($file_info);
//
//            if ($ue) {
//                \Ret::succ(['src' => $sav]);
//            } else {
//                \Ret::succ($sav);
//            }
//        } else {
//            return [
//                'code' => 507,
//                'data' => '图片格式编码错误'
//            ];
//        }
//    }

    /**
     * @param mixed $full
     * @param $url
     * @param AttachmentModel $file_exists
     * @param mixed $type
     * @return string
     */
    public function getStr(mixed $full, $url, AttachmentModel $file_exists, mixed $type): string
    {
        $sav = ($full ? $url . '/' : '') . $file_exists['path'];
        // 附件已存在
        switch ($type) {
            case "ue":
                \Ret::Success(0, ['src' => $sav]);
                break;

            case "complete":
                $file_exists["src"] = $file_exists['path'];
                $file_exists["url"] = $url . '/' . $file_exists['path'];
                $file_exists["surl"] = $file_exists['path'];
                \Ret::Success(0, $file_exists);
                break;

            default:
                \Ret::Success(0, $sav);
                break;
        }
        return $sav;
    }


}
