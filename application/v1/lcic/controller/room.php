<?php

namespace app\v1\lcic\controller;

use app\v1\lcic\model\LcicUserModel;
use Ret;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Lcic\V20220817\Models\CreateRoomRequest;


class room extends user
{

    public function auto()
    {
        $OriginId = \Input::Post('OriginId');
        $user = LcicUserModel::where('project', $this->token)->where('OriginId', $OriginId)->findOrEmpty();
        if ($user->isEmpty()) {
            $this->create();
        } else {
            $this->modify();
        }
    }

    public function create()
    {
        try {
            $req = new CreateRoomRequest();

            $params = array(
                'Name' => 'sadsdasd',
                'StartTime' => 1234212,
                'EndTime' => 12312314,
                'TeacherId' => 'dsaswdadqdqwdqwdwqdw',
                'SdkAppId' => 123123,
                'Resolution' => 1,
                'MaxMicNumber' => 16,
                'AutoMic' => 0,
                'AudioQuality' => 0,
                'SubType' => 'videodoc',
                'DisableRecord' => 1
            );
            $req->fromJsonString(json_encode($params));
            $resp = $this->client->CreateRoom($req);

            // 输出json格式的字符串回包
            Ret::Success(0, $resp, $resp->getRoomId());
        } catch (TencentCloudSDKException $e) {
            Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
        }
    }

}