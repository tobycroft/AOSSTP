<?php

namespace WlwxSMS;

use think\Exception;

class Send
{

    public static function full_text($password, $cust_code, $content, $destMobiles): array
    {
        //发送的URL
        $url = 'https://smsapp.wlwx.com/sendSms';
        //发送数据
        $data = [];
        $data['cust_code'] = $cust_code;                      //账号唯一标识
        $data['content'] = $content;                         //发送内容
        $data['destMobiles'] = $destMobiles;                        //手机号码，多个用逗号‘,’隔开，最多1000个
        $data['sign'] = strtoupper(md5($content . $password)); //签名
//        echo json_encode($data);
//        exit();
        $back = self::post($url, $data);
        //输出结果
        return json_decode($back, 1);
    }


    /**
     * @throws Exception
     */
    protected static function post($url, $postData)
    {
        if (!is_array($postData)) {
            return FALSE;
        }
        //初始化curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);    //>设置请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //>设置为返回请求内容

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);  //>运行curl
        if ($response === false) {
            if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                throw new Exception('云短信平台超时');
            }
        }
        if (empty($response)) {
            if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                throw new Exception('短信平台无返回');
            }
        }
        curl_close($ch);        //>关闭curl
        return $response;
    }

}