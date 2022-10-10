<?php

namespace app\v1\image\controller;

use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use chillerlan\QRCode\Output\QRImage;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
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
        \think\facade\Response::contentType("image/png")->send();
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
            'bgColor' => [200, 200, 200],
            'imageTransparent' => false,
            'drawCircularModules' => true,
            'circleRadius' => 0.8,
            'addLogoSpace' => true,
        ]);
        $qr = new QRCode($opt);
        $mat = $qr->getMatrix($json);
        $mat->setLogoSpace(10, 10, null, null);

        $qrp = new QRImageWithLogo($opt, $mat);
        echo $qrp->dump(null, $url);
//        echo $qlogo->dump(null, $url);
//        $im = file_get_contents($url);
//        $im = imagecreatefromstring($im);
//        imagepng($im);
        \think\facade\Response::contentType("image/png")->send();
    }

}

class QRImageWithLogo extends QRImage
{

    /**
     * @param string|null $file
     * @param string|null $logo
     *
     * @return string
     * @throws \chillerlan\QRCode\Output\QRCodeOutputException
     */
    public function dump(string $file = null, string $logo = null): string
    {
        // set returnResource to true to skip further processing for now
        $this->options->returnResource = true;

        // of course you could accept other formats too (such as resource or Imagick)
        // i'm not checking for the file type either for simplicity reasons (assuming PNG)
//        $logo = file_get_contents($logo);
//        if (!is_file($logo) || !is_readable($logo)) {
//            throw new QRCodeOutputException('invalid logo');
//        }

        // there's no need to save the result of dump() into $this->image here
        parent::dump($file);
        $im = file_get_contents($logo);
        $im = imagecreatefromstring($im);
        // get logo image size
        $w = imagesx($im);
        $h = imagesy($im);

        // set new logo size, leave a border of 1 module (no proportional resize/centering)
        $lw = ($this->options->logoSpaceWidth - 2) * $this->options->scale;
        $lh = ($this->options->logoSpaceHeight - 2) * $this->options->scale;

        // get the qrcode size
        $ql = $this->matrix->size() * $this->options->scale;
//        $this->image = $im;
        // scale the logo and copy it over. done!
//        imagecopyresampled($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, $w, $h);
        $this->image = $im;
        imagecopymerge($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, 75);
        $imageData = $this->dumpImage();

        if ($file !== null) {
            $this->saveToFile($imageData, $file);
        }

        if ($this->options->imageBase64) {
            $imageData = $this->toBase64DataURI($imageData, 'image/' . $this->options->outputType);
        }

        return $imageData;
    }

}