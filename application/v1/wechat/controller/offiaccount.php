<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\qr;
use app\v1\wechat\model\WechatModel;
use think\cache\driver\Redis;
use think\Request;
use Wechat\Miniprogram;
use Wechat\OfficialAccount;

class offiaccount extends info
{


    public string $path_prefix = './upload/';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $expire_after = strtotime($this->wechat['expire_after']);
        if ($expire_after < time() || empty($this->access_token)) {
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
//                echo $data->error();
//                exit();
                \Ret::Fail(201, 'accesskey过期', 'accesskey过期');
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
        if (!$openid = input('openid')) {
            \Ret::Fail(400, null, 'openid');
        }

        $wxa = OfficialAccount::userinfo($this->access_token, $openid);
        if ($wxa->isSuccess()) {
            \Ret::Success(0, $wxa->getData());
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function openid_url()
    {
        if (!$redirect_uri = input('redirect_uri')) {
            \Ret::Fail(400, null, 'redirect_uri');
        }
        if (!$response_type = input('response_type')) {
            \Ret::Fail(400, null, 'response_type');
        }
        if (!$scope = input('scope')) {
            \Ret::Fail(400, null, 'scope');
        }
        if (!$state = input('state')) {
            \Ret::Fail(400, null, 'state');
        }
        $png = input('png');
        $appid = $this->appid;
        $redirect_uri = urlencode($redirect_uri);
        $combine = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=$response_type&scope=$scope&state=$state#wechat_redirect";
        if ($png == "1") {
            $qr = new qr();
            $qr->qr_png($combine);
        } else {
            \Ret::Success(0, $combine);
        }

    }

    public function openid_readback()
    {
        $code = input("code");
        $state = input("state");
        echo $code, $state;
    }

    public function openid_aquire(Request $request)
    {
        if (!$request->has('code')) {
            \Ret::Fail(400, null, 'code');
        }
        $code = input('code');

        $wxa = OfficialAccount::offiAccessToken($this->appid, $this->appsecret, $code, "authorization_code");
        if ($wxa->isSuccess()) {
            \Ret::Success(0, $wxa->openid);
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function template_push()
    {
        if (!$openid = input('openid')) {
            \Ret::Fail(400, null, 'openid');
        }
        if (!$template_id = input('template_id')) {
            \Ret::Fail(400, null, 'template_id');
        }
        if (!$url = input('url')) {
            \Ret::Fail(400, null, 'url');
        }
        if (!$data = input('data')) {
            \Ret::Fail(400, null, 'data');
        }
        $wxa = OfficialAccount::uniform_send($this->access_token, $openid, $template_id, $url, $data);
        if ($wxa->isSuccess()) {
            \Ret::Success(0, $wxa->getData());
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function template_push_more()
    {
        if (!$openids = input('openids')) {
            \Ret::Fail(400, null, 'openids');
        }
        if (!$template_id = input('template_id')) {
            \Ret::Fail(400, null, 'template_id');
        }
        if (!$url = input('url')) {
            \Ret::Fail(400, null, 'url');
        }
        if (!$data = input('data')) {
            \Ret::Fail(400, null, 'data');
        }
//        $redis = new \Redis();
        $rhan = new Redis();
        $redis = $rhan->handler();
        $ids = json_decode($openids, 1);
        if (count($ids) < 1) {
            \Ret::Fail(400, null, "openids should contains more than 1");
        }
        foreach ($ids as $id) {
            $redis->lPush("__offiaccountpush__", json_encode(
                    [
                        'openid' => $id,
                        'template_id' => $template_id,
                        'url' => $url,
                        'data' => $data,
                    ]
                )
            );
        }
        \Ret::Success(0);
    }
}