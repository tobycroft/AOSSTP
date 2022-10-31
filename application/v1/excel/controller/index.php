<?php

namespace app\v1\excel\controller;


use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\facade\Validate;
use think\Request;

class index extends CommonController
{

    public $token;
    public $proc;

    public function initialize()
    {
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::Fail(401, null, 'token');
        }
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::Fail(400, null, '项目不可用');
            return;
        }

    }

    public function index(Request $request)
    {
        $file = $request->file("file");
        if (!$file) {
            \Ret::Fail(400, null, 'file字段没有用文件提交');
            return;
        }
        $hash = $file->hash('md5');
        if (!Validate::fileExt($file, ["xls", "xlsx"])) {
            \Ret::Fail(406, null, "ext not allow");
            return;
        }
        if (!Validate::fileSize($file, (float)8192 * 1024)) {
            \Ret::Fail(406, null, "size too big");
            return;
        }

        $info = $file->move('./upload/excel/' . $this->token);
        $reader = IOFactory::load($info->getPathname());
        unlink($info->getPathname());
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
        echo json_encode($colums);
    }

    public function dp(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            \Ret::Fail(400, null, 'file字段没有用文件提交');
            return;
        }
        $hash = $file->hash('md5');
        if (!Validate::fileExt($file, ['xls', 'xlsx'])) {
            \Ret::Fail(406, null, 'ext not allow');
            return;
        }
        if (!Validate::fileSize($file, (float)8192 * 1024)) {
            \Ret::Fail(406, null, 'size too big');
            return;
        }

        $info = $file->move('./upload/excel/' . $this->token);
        $reader = IOFactory::load($info->getPathname());
        unlink($info->getPathname());
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
        \Ret::Success(0, $colums);
    }

    public function url(Request $request)
    {
        $file_url = input('file_url');
        if (!$file_url) {
            \Ret::Fail(400, null, '');
            return;
        }
        if (!preg_match('/([a-f\d]{32}|[A-F\d]{32})/', $file_url, $matches)) {
            \Ret::Fail(404, null, '文件未上传');
            return;
        }
        $file_info = AttachmentModel::where('md5', $matches[0])->find();
        $info = $file_url->move('./upload/excel/' . $this->token);
        $reader = IOFactory::load('./upload/' . $this->token . DIRECTORY_SEPARATOR . $file_info['path']);
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
        \Ret::Success(0, $colums);
    }

    public function force(Request $request)
    {
        $file = $request->file("file");
        if (!$file) {
            \Ret::Fail(400, null, 'file字段没有用文件提交');
            return;
        }
        $hash = $file->hash('md5');
        if (!Validate::fileExt($file, ["xls", "xlsx"])) {
            \Ret::Fail(406, null, "ext not allow");
            return;
        }
        if (!Validate::fileSize($file, (float)8192 * 1024)) {
            \Ret::Fail(406, null, "size too big");
            return;
        }
        $info = $file->move('./upload/excel/' . $this->token);
        $reader = IOFactory::load($info->getPathname());
        unlink($info->getPathname());
        $datas = $reader->getActiveSheet()->toArray();
        if (count($datas) < 2) {
            \Ret::Fail(400, null, "表格长度不足");
            return;
        }
        $keys = [];
        foreach ($datas[0] as $data) {
            if (!empty($data)) {
                $keys[] = $data;
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
        echo json_encode($colums);
    }


}
