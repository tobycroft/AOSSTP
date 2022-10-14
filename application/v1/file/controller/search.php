<?php

namespace app\v1\file\controller;

use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;

class search extends CommonController
{

    public function initialize()
    {
        parent::initialize();
    }

    public function md5()
    {
        $token = $this->token;
        $proc = ProjectModel::api_find_token($token);
        $md5 = input("md5");
        if (empty($md5)) {
            \Ret::Fail("需要md5字段");
        }
        $file_exists = AttachmentModel::where("md5", $md5)->where("sha1", "<>", '')->find();
        if (empty($file_exists)) {
            \Ret::Fail("未找到文件，请先上传");
        }
        $file_exists["src"] = $file_exists['path'];
        $file_exists["url"] = $proc['url'] . '/' . $file_exists['path'];
        $file_exists["surl"] = $file_exists['path'];
        \Ret::Success($file_exists);
    }
}