<?php

namespace app\v1\wechat\controller;

use app\v1\log\model\LogWebModel;
use app\v1\logger\model\LoggerErrModel;
use app\v1\wechat\model\WechatMessageModel;
use app\v1\wechat\model\WechatUserModel;
use Input;
use Ret;
use Throwable;

class api extends info
{
    protected $signature;

    protected $timestamp;
    protected $nonce;
    protected $wechat_token;

    public function initialize()
    {
        parent::initialize();
        $this->wechat_token = $this->wechat['token'];

        //微信验证
        $in = Input::Raw();
        LogWebModel::create([
            'get' => json_encode(request()->get()),
            'post' => json_encode(request()->post()),
            'raw' => $in,
            'header' => json_encode(request()->header()),
            'method' => request()->method(),
        ]);

        $this->signature = Input::Get('signature');
        $this->timestamp = Input::Get('timestamp');
        $this->nonce = Input::Get('nonce');

        $tmpArr = array($this->wechat_token, $this->timestamp, $this->nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr != $this->signature) {
            Ret::Fail(403);
        }
    }

    public function recv()
    {
        if (request()->isGet()) {
            $this->verify();
        } else {
            $this->message();
        }
    }

    public function message()
    {
        $xmltext = Input::Raw();
        $xml_data = simplexml_load_string($xmltext, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        $encode_data = json_encode($xml_data);
        $data = json_decode($encode_data, 1);
        $data['project'] = $this->wechat['project'];
        $data['raw'] = $encode_data;
        $create_data = WechatMessageModel::create($data);


        $openid = $data["FromUserName"];
        $wechat_user = WechatUserModel::where("openid", $openid)->find();
        if (!$wechat_user) {
            $wechat_user = WechatUserModel::create([
                'project' => $this->wechat['project'],
                'openid' => $openid,
                'is_suscribe' => 1,
            ]);
        }
        switch ($data['MsgType']) {
            case "text":
                break;

            case "image":
                break;

            case "voice":
                break;

            case "video":
                break;

            case "shortvideo":
                break;

            case "location":
                break;

            case "link":
                break;

            case "event":
                switch ($data["Event"]) {
                    case "subscribe":
                        WechatUserModel::where("openid", $openid)->data("is_suscribe", 1)->update();
                        break;

                    case "unsubscribe":
                        WechatUserModel::where('openid', $openid)->data('is_suscribe', 0)->update();
                        break;

                    case "SCAN":
                        if ($wechat_user["is_suscribe"] == 0) {
                            WechatUserModel::where('openid', $openid)->data('is_suscribe', 1)->update();
                        }
                        break;

                    case "LOCATION":
                        break;

                    case "CLICK":
                        break;

                    case "VIEW":
                        break;

                    default:
                        return "fail";

                }
                break;


            default:
//                WechatMessageModel::create($json);
                break;
        }


        if (!empty($this->wechat['message_url'])) {
            try {
                raw_post($this->wechat['message_url'], [], $create_data->toArray());
                $create_data->is_send = 1;
                $create_data->save();
            } catch (Throwable $e) {
                LoggerErrModel::create([
                    'project' => $this->proc['token'],
                    'log' => $e->getTraceAsString(),
                    'discript' => $e->getMessage(),
                ]);
            }
        }
//        echo json_encode($create_data, 320);
        echo "success";
    }


    public function verify()
    {
        echo Input::Get('echostr');
    }

}

function raw_post(string $base_url, array $query = [], array $postData = [])
{
    $send_url = $base_url;
    if (!empty($query)) {
        $send_url .= '?' . http_build_query($query);
    }
    $headers = array('Content-type: application/json;charset=UTF-8', 'Accept: application/json', 'Cache-Control: no-cache', 'Pragma: no-cache');
    $postData = json_encode($postData, 320);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $send_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}