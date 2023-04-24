<?php

namespace app\v1\live\action;

class GetPushUrl
{
    public static function getPushUrl($domain, $streamName, $key = null, $time = null)
    {
        if ($key && $time) {
            $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
//            txSecret = MD5( KEY + streamName + txTime )
            $txSecret = md5($key . $streamName . $txTime);
            $ext_str = '?' . http_build_query(array(
                    'txSecret' => $txSecret,
                    'txTime' => $txTime
                ));
        }
        return 'rtmp://' . $domain . '/live/' . $streamName . (isset($ext_str) ? $ext_str : '');
    }

}