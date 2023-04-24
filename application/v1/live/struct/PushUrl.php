<?php

namespace app\v1\live\struct;

class PushUrl
{
    public string $rtmp = "";
    public string $obs_server = "";
    public string $stream_code = "";
    public string $webrtc = "";
    public string $srt = "";
    public string $rtmp_over_srt = "";


    public function __construct($domain, $streamName, $key, $time)
    {
        $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
//            txSecret = MD5( KEY + streamName + txTime )
        $txSecret = md5($key . $streamName . $txTime);
        $ext_str = '?' . http_build_query(array(
                'txSecret' => $txSecret,
                'txTime' => $txTime
            ));
        $this->obs_server = $domain;
        $this->stream_code = $streamName . (isset($ext_str) ? $ext_str : '');
        $this->rtmp = 'rtmp://' . $this->obs_server . '/live/' . $this->stream_code;
        $this->rtmp_over_srt = 'rtmp://' . $this->obs_server . ':3570/live/' . $this->stream_code;
        $this->srt = 'srt://' . $this->obs_server . ':9000?streamid=#!::h=' . $this->obs_server . ',r=live/' . $streamName . ',txSecret=' . $txSecret . ',txTime=' . $txTime;
        $this->webrtc = 'webrtc://' . $this->obs_server . '/live/' . $this->stream_code;
    }
}