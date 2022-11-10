<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\create;
use app\v1\image\controller\qr;
use app\v1\wechat\model\WechatModel;
use think\Request;
use Wechat\Miniprogram;
use Wechat\OfficialAccount;

class offiaccount extends create
{

    public mixed $access_token;
    public string $appid;
    public string $appsecret;
    public string $path_prefix = './upload/';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $wechat = WechatModel::where('project', $this->token)->find();
        if (!$wechat) {
            \Ret::Fail(404, null, '未找到项目');
        }
        $this->appid = $wechat['appid'];
        $this->appsecret = $wechat['appsecret'];
        $this->access_token = $wechat['access_token'];

        $expire_after = strtotime($wechat['expire_after']);
        if ($expire_after < time() || empty($wechat['access_token'])) {
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

    public function user_list(Request $request)
    {
        if (!$request->has('next_openid')) {
            \Ret::Fail(400, null, 'next_openid');
        }
        $next_openid = input('next_openid');

        $wxa = OfficialAccount::userlist($this->access_token, "");
        if ($wxa->isSuccess()) {
            \Ret::Success(0, $wxa->openid);
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function user_info(Request $request)
    {
        if (!$request->has('openid')) {
            \Ret::Fail(400, null, 'openid');
        }
        $openid = input('openid');

        $wxa = OfficialAccount::userinfo($this->access_token, $openid);
        if ($wxa->isSuccess()) {
            \Ret::Success(0, $wxa->getData());
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function openid_url()
    {
        $appid = $this->appid;
        $redirect_uri = 'https://upload.tuuz.cc/v1/open/index/wechat';
        $response_type = 'code';
        $scope = 'snsapi_base';
        $state = '123';
        $redirect_uri = urlencode($redirect_uri);
        $combine = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=$response_type&scope=$scope&state=$state#wechat_redirect";
        $qr = new qr();
        $qr->qr_png($combine);
    }

    public function openid_readback()
    {
        $code = input("code");
        $state = input("state");
        echo $code, $state;
    }
}