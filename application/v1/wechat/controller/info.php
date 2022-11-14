<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\create;
use app\v1\wechat\model\WechatModel;
use think\Request;
use Wechat\Miniprogram;

class info extends create
{

    public function auto_accesskey(Request $request)
    {
        $data = Miniprogram::getAccessToken($this->appid, $this->appsecret);
        if ($data->isSuccess()) {
            $this->access_token = $data->access_token;
            WechatModel::where('project', $this->token)->data(
                [
                    'access_token' => $data->access_token,
                    'expire_after' => date('Y-m-d H:i:s', $data->expires_in + time() - 600)
                ]
            )->update();
        } else {
            echo $data->error();
            exit();
        }
    }


    public function set_accesskey(Request $request)
    {
        $data = Miniprogram::getAccessToken($this->appid, $this->appsecret);
        if ($data->isSuccess()) {
            $this->access_token = $data->access_token;
            WechatModel::where('project', $this->token)->data(
                [
                    'access_token' => $data->access_token,
                    'expire_after' => date('Y-m-d H:i:s', $data->expires_in + time() - 600)
                ]
            )->update();
        } else {
            echo $data->error();
            exit();
        }
    }


}