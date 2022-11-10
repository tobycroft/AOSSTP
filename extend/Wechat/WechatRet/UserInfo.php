<?php

namespace Wechat\WechatRet;


class UserInfo
{
    public $response;

    protected $data;
    protected mixed $error;

    public int $subscribe;
    public string $openid;
    public string $nickname;
    public int $sex;
    public string $language;

    public string $city;
    public string $province;
    public string $country;

    public string $headimgurl;
    public int $subscribe_time;
    public string $subscribe_scene;

    /*
     * {
    'subscribe': 1,
    'openid': 'oWboX6gK1VcGcIj5jG_XKEQVx4Fc',
    'nickname': '',
    'sex': 0,
    'language': 'zh_CN',
    'city': '',
    'province': '',
    'country': '',
    'headimgurl': '',
    'subscribe_time': 1668071043,
    'remark': '',
    'groupid': 0,
    'tagid_list': [],
    'subscribe_scene': 'ADD_SCENE_QR_CODE',
    'qr_scene': 0,
    'qr_scene_str': ''
}
     */

    public function __construct($json)
    {
        $this->response = $json;
        $data = json_decode($json, 1);
        if (isset($data['errmsg'])) {
            $this->error = $data['errmsg'];
        } else {
            $this->data = $data;
//            $this->openid = $this->data['openid'] ?? "";
//            $this->session_key = $this->data['session_key'] ?? "";
//            $this->unionid = $this->data['unionid'] ?? "";
        }
    }

    public function isSuccess()
    {
        if (isset($this->error)) {
            return false;
        } else {
            return true;
        }
    }

    public function getError()
    {
        return $this->error;
    }

}