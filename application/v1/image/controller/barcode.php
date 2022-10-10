<?php

namespace app\v1\image\controller;

use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use Picqer\Barcode as bc;
use think\Request;

class barcode extends CommonController
{


    public mixed $token;
    public mixed $proc;

    public function initialize()
    {
        set_time_limit(0);
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::fail('项目不可用');
        }
    }

    public function png(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        $json = $request->post("data");
        $generator = new bc\BarcodeGeneratorPNG();
        echo $generator->getBarcode($json, $generator::TYPE_CODE_128);
//        \think\facade\Response::contentType("image/png")->send();
    }


}