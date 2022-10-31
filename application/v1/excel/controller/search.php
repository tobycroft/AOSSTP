<?php

namespace app\v1\excel\controller;

use app\v1\excel\model\ExcelModel;
use app\v1\file\model\AttachmentModel;
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
        $file = file_get_contents($this->proc["url"] . '/' . $file_info['path']);
        $savname = './upload/excel/' . $this->token . DIRECTORY_SEPARATOR . $md5 . '.' . $file_info['ext'];
        if (file_put_contents($savname, $file)) {
            $reader = IOFactory::load($savname);
            $datas = $reader->getActiveSheet()->toArray();
            if (count($datas) < 2) {
                \Ret::Fail(400, null, '表格长度不足');
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
                    \Ret::Fail(400, null, '表格长度不一');
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
                    $arr[$keys[$s]] = $line[$s] ?: '';
                }
                $colums[] = $arr;
            }
            ExcelModel::create([
                "project" => $this->token,
                "md5" => $md5,
                "value" => $colums
            ]);
            \Ret::Success(0, $colums);
        }

    }
}