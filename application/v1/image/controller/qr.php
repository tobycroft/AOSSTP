<?php

namespace app\v1\image\controller;

use app\v1\image\action\QRImageWithLogo;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use think\facade\Response;
use think\Request;

class qr extends CommonController
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
        $json = input("data");
        $opt = new QROptions([
            'version' => 7,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 7,
            'imageBase64' => false,
            'bgColor' => [200, 200, 200],
            'imageTransparent' => false,
            'drawCircularModules' => true,
            'circleRadius' => 0.8,
        ]);
        $qr = new QRCode($opt);

        echo $qr->render($json);
        Response::contentType("image/png")->send();
    }

    public function base64(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        $json = input("data");
        $opt = new QROptions([
            'version' => 7,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 7,
            'imageBase64' => false,
            'bgColor' => [200, 200, 200],
            'imageTransparent' => false,
            'drawCircularModules' => true,
            'circleRadius' => 0.8,
        ]);
        $qr = new QRCode($opt);

        echo base64_encode($qr->render($json));
    }

    public function logo(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        if (!$request->has("url")) {
            \Ret::fail("url");
        }
        $json = input("data");
        $url = input("url");
        $opt = new QROptions([
            'version' => 10,
            'eccLevel' => QRCode::ECC_H,
            'scale' => 7,
            'imageBase64' => false,
            'bgColor' => [255, 255, 255],
            'imageTransparent' => false,
            'drawCircularModules' => true,
            'circleRadius' => 0.8,
            'addLogoSpace' => true,
        ]);
        $qr = new QRCode($opt);
        $mat = $qr->getMatrix($json);
//        $mat->setLogoSpace(10, 10, null, null);

        $qrp = new QRImageWithLogo($opt, $mat);
        echo $qrp->dump(null, $url);
        Response::contentType("image/png")->send();
    }

}

