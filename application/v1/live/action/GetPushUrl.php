<?php

namespace app\v1\live\action;

use app\v1\live\struct\AllUrl;
use app\v1\live\struct\PushUrl;

class GetPushUrl
{
    public static function getPushUrl($domain, $streamName, $key = null, $time = null)
    {
        $push = new PushUrl($domain, $streamName, $key, $time);
        return $push->rtmp;
    }

    public static function getAll($domain, $play_domain, $streamName, $push_key, $play_key, $time): AllUrl
    {
        return new AllUrl($domain, $play_domain, $streamName, $push_key, $play_key, $time);
    }

}