<?php

namespace app\v1\lcic\controller;

use app\v1\lcic\model\LcicUserModel;
use app\v1\lcic\model\LclcRoomModel;
use Input;
use Ret;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Lcic\V20220817\Models\CreateRoomRequest;
use TencentCloud\Lcic\V20220817\Models\DeleteRoomRequest;
use TencentCloud\Lcic\V20220817\Models\ModifyRoomRequest;


class room extends user
{

    public function auto()
    {
        $Name = Input::PostInt('Name');
        $TeacherId = Input::PostInt('TeacherId');
        $OriginId = Input::Post('TeacherId');
        $StartTime = Input::PostInt('StartTime');
        $EndTime = Input::PostInt('EndTime');
        $user = LcicUserModel::where('project', $this->token)->where(['OriginId' => $TeacherId])->findOrEmpty();
        if ($user->isEmpty()) {
            $this->create();
        } else {
            $this->modify();
        }
    }

    public function create()
    {
        $Name = Input::PostInt("Name");
        $TeacherId = Input::PostInt("TeacherId");
        $StartTime = Input::PostInt("StartTime");
        $EndTime = Input::PostInt("EndTime");
        $user = LcicUserModel::where('project', $this->token)->where(['OriginId' => $TeacherId])->findOrEmpty();
        if ($user->isEmpty()) {
            Ret::Fail(404, null, "教师用户不存在，请先添加");
        }
        if ($EndTime - $StartTime > 18000) {
            $EndTime = $StartTime + 18000;
        }
        try {
            $req = new CreateRoomRequest();
            $params = array(
                'Name' => $Name,
                'StartTime' => $StartTime,
                'EndTime' => $EndTime,
                'TeacherId' => $user["UserId"],
                'SdkAppId' => $this->sdkappid,
                'Resolution' => 1,
                'MaxMicNumber' => 16,
                'AutoMic' => 0,
                'AudioQuality' => 0,
                'SubType' => 'videodoc',
                'DisableRecord' => 1
            );
            $req->fromJsonString(json_encode($params));
            $resp = $this->client->CreateRoom($req);
            LclcRoomModel::create([
                'Name' => $Name,
                'StartTime' => $StartTime,
                'EndTime' => $EndTime,
                'TeacherId' => $user['UserId'],
                'SdkAppId' => $this->sdkappid,
                'Resolution' => 1,
                'MaxMicNumber' => 16,
                'AutoMic' => 0,
                'AudioQuality' => 0,
                'SubType' => 'videodoc',
                'DisableRecord' => 1
            ]);
            // 输出json格式的字符串回包
            Ret::Success(0, $resp, $resp->getRoomId());
        } catch (TencentCloudSDKException $e) {
            Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
        }
    }

    public function modify()
    {
        $RoomId = Input::PostInt('RoomId');
        $Name = Input::PostInt('Name');
        $TeacherId = Input::PostInt('TeacherId');
        $StartTime = Input::PostInt('StartTime');
        $EndTime = Input::PostInt('EndTime');
        $user = LcicUserModel::where('project', $this->token)->where(['OriginId' => $TeacherId])->findOrEmpty();
        if ($user->isEmpty()) {
            Ret::Fail(404, null, '教师用户不存在，请先添加');
        }
        if ($EndTime - $StartTime > 18000) {
            $EndTime = $StartTime + 18000;
        }
        try {
            $req = new ModifyRoomRequest();
            $params = array(
                'RoomId' => $RoomId,
                'Name' => $Name,
                'StartTime' => $StartTime,
                'EndTime' => $EndTime,
                'TeacherId' => $user['UserId'],
                'SdkAppId' => $this->sdkappid,
                'Resolution' => 1,
                'MaxMicNumber' => 16,
                'AutoMic' => 0,
                'AudioQuality' => 0,
                'SubType' => 'videodoc',
                'DisableRecord' => 1
            );
            $req->fromJsonString(json_encode($params));
            $resp = $this->client->ModifyRoom($req);

            // 输出json格式的字符串回包
            Ret::Success(0, $resp, $resp->getRoomId());
        } catch (TencentCloudSDKException $e) {
            Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $req = new DeleteRoomRequest();

            $params = array(
                'RoomId' => 123,
            );
            $req->fromJsonString(json_encode($params));
            $resp = $this->client->DeleteRoom($req);

            // 输出json格式的字符串回包
            Ret::Success(0, $resp, $resp->getRoomId());
        } catch (TencentCloudSDKException $e) {
            Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
        }
    }

}