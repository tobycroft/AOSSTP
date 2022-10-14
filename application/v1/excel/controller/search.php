<?php

namespace app\v1\file\controller;

use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            \Ret::Fail(400, null, "需要md5字段");
        }
        $file_exists = AttachmentModel::where("md5", $md5)->where("sha1", "<>", '')->find();
        if (empty($file_exists)) {
            \Ret::Fail(404, null, "未找到文件,请先上传");
        }
        $file_exists["src"] = $file_exists['path'];
        $file_exists["url"] = $proc['url'] . '/' . $file_exists['path'];
        $file_exists["surl"] = $file_exists['path'];


        $reader = IOFactory::load('./upload/excel/');
        $datas = $reader->getActiveSheet()->toArray();
        if (count($datas) < 2) {
            \Ret::Fail(400, null, "表格长度不足");
            return;
        }
        $value = [];
        $i = 0;
        $keys = [];
        foreach ($datas[0] as $data) {
            if (!empty($data)) {
                $keys[] = $data;
            }
        }
        foreach ($keys as $key) {
            if (empty($key)) {
                \Ret::Fail(400, null, "表格长度不一");
                return;
            }
        }
        $count_column = count($keys);
        $colums = [];
        for ($i = 1; $i < count($datas); $i++) {
            $line = $datas[$i];
            if (empty($line[0])) {
                continue;
            }
            for ($s = 0; $s < $count_column; $s++) {
                $arr[$keys[$s]] = $line[$s] ?: "";
            }
            $colums[] = $arr;
        }
        \Ret::Success(json_encode($colums));
    }
}