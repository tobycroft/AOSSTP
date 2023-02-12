<?php

namespace Wechat;

use Wechat\WechatRet\Template\UniformSend;

class KefuMessage extends Miniprogram
{

    public static function custom_send(string $access_token, $touser, $msgtype, $content, $media_id, $thumb_media_id, $title, $description): UniformSend
    {
        $send = [
            'touser' => $touser,
            'msgtype' => $msgtype,
        ];
        switch ($msgtype) {
            case "text":
                $send[$msgtype] = [
                    'content' => $content
                ];
                break;

            case "image":
            case "voice":
                $send[$msgtype] = [
                    'media_id' => $media_id
                ];
                break;

            case "video":
                $send[$msgtype] = [
                    'media_id' => $media_id,
                    'thumb_media_id' => $thumb_media_id,
                    'title' => $title,
                    'description' => $description,
                ];
                break;


        }
        if (!empty($miniprogram_struct)) {
            $send['miniprogram'] = [
                'appid' => $miniprogram_struct->appid,
                'pagepath' => $miniprogram_struct->pagepath,
            ];
        }
        if (!empty($client_msg_id)) {
            $send['client_msg_id'] = $client_msg_id;
        }
        return new UniformSend(raw_post(self::$Base . self::$message_send,
            [
                'access_token' => $access_token,
            ],
            $send
        ));
    }
}