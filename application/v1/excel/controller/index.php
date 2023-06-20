<?php

namespace app\v1\excel\controller;


use app\v1\file\action\OssSelectionAction;
use app\v1\file\model\AttachmentModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ret;
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
            Ret::Fail(401, null, '项目不可用');
        }
        $this->proc = OssSelectionAction::App_find_byProc($this->proc);

    }

    public function create()
    {
        $data = \Input::Post("data");

        # 实例化 Spreadsheet 对象
        $spreadsheet = new Spreadsheet();

        # 获取活动工作薄
        $sheet = $spreadsheet->getActiveSheet();

        # 获取单元格
        $cellA = $sheet->getCell('A1');

        # 设置单元格值
        $cellA->setValue('欧阳克');

        # 获取单元格
        $cellB = $sheet->getCellByColumnAndRow(1, 2);

        # 设置单元格值
        $cellB->setValue('黄蓉');

        # 获取设置单元格，链式操作
        $sheet->getCell('A3')->setValue('郭靖');
        $sheet->getCellByColumnAndRow(1, 4)->setValue('杨康');

        # Xlsx类 将电子表格保存到文件

        $writer = new Xlsx($spreadsheet);
        $writer->save('./upload/excel/' . $this->token . '/tempfile/' . time() . ".xlsx");
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
        $this->extracted($reader);
    }

    public function md5(Request $request)
    {
        $md5 = input('md5');
        if (!$md5) {
            \Ret::Fail(400, null, 'md5');
            return;
        }
        $file_info = AttachmentModel::where('md5', $md5)->find();
        if (!$file_info || !file_exists('./upload/' . $file_info['path'])) {
            \Ret::Fail("404", null, "文件未被上传或不属于本系统");
            return;
        }
        $reader = IOFactory::load('./upload/' . $file_info['path']);
        $this->extracted($reader);
    }

    public function remote(Request $request)
    {
        $md5 = input('md5');
        if (!$md5) {
            \Ret::Fail(400, null, 'md5');
            return;
        }
        $file_info = AttachmentModel::where('md5', $md5)->find();
        if (!$file_info || !file_exists('./upload/' . $file_info['path'])) {
            \Ret::Fail("404", null, "文件未被上传或不属于本系统");
            return;
        }
        $reader = IOFactory::load('./upload/' . $file_info['path']);
        $this->extracted($reader);
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

    /**
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $reader
     * @return void
     */
    public function extracted(\PhpOffice\PhpSpreadsheet\Spreadsheet $reader): void
    {
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


}
