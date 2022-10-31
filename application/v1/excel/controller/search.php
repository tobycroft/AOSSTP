<?php

namespace app\v1\excel\controller;

use app\v1\file\model\AttachmentModel;
use app\v1\file\model\ExcelModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\Request;

class search extends index
{

    public function md5(Request $request)
    {
        $md5 = input('md5');
        if (!$md5) {
            \Ret::Fail(400, null, 'md5');
            return;
        }
        $file_info = AttachmentModel::where('md5', $md5)->find();
        if (!$file_info) {
            \Ret::Fail('404', null, '文件未被上传或不属于本系统');
        }

        $excel_info = ExcelModel::where("md5", $md5)->find();
        if ($excel_info) {
            \Ret::Success(0, json_decode($excel_info['value'], 1));
        }
        if ($this->proc["type"] == "all" && !file_exists('./upload/' . $file_info['path'])) {
            \Ret::Fail('404', null, '本地文件不存在');
        }
        $file = file_get_contents("");
        $info = $file->move('./upload/excel/' . $this->token);
        $reader = IOFactory::load('./upload/' . $file_info['path']);
        $this->extracted($reader);
    }
}