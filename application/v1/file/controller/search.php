<?php

namespace app\v1\file\controller;

use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;

class search extends CommonController
{

    protected mixed $token;
    protected mixed $proc;

    public function initialize()
    {
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::Fail(401, null, 'token');
        }
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::Fail(401, null, '项目不可用');
        }
    }

    public function md5()
    {
        $token = $this->token;
        $proc = $this->proc;
        $md5 = input("md5");
        if (empty($md5)) {
            \Ret::Fail(400, null, "需要md5字段");
        }
        $file_exists = AttachmentModel::where("md5", $md5)->where("sha1", "<>", '')->find();
        if (empty($file_exists)) {
            \Ret::Fail(404, null, "未找到文件,请先上传");
        }
        $file_exists["src"] = $file_exists['path'];
        $file_exists["url"] = $proc['url'] . '/' . $file_exists['path'];
        $file_exists["surl"] = $file_exists['path'];
        \Ret::Success(0, $file_exists);
    }
}