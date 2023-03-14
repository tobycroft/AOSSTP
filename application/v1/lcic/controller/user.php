<?php

namespace app\v1\lcic\controller;

use app\v1\image\controller\create;
use app\v1\lcic\model\LcicModel;
use app\v1\lcic\model\LcicUserModel;
use Ret;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Lcic\V20220817\LcicClient;
use TencentCloud\Lcic\V20220817\Models\ModifyUserProfileRequest;
use TencentCloud\Lcic\V20220817\Models\RegisterUserRequest;


class user extends create
{

    public string|null $appid;
    public string|null $secretid;
    public string|null $secretkey;
    public string|null $end_point;

    public int|null $sdkappid;

    protected mixed $lcic;

    protected Credential $cred;
    protected HttpProfile $httpProfile;
    protected ClientProfile $clientProfile;

    protected LcicClient $client;


    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->lcic = LcicModel::where('project', $this->token)->find();
        if (!$this->lcic) {
            Ret::Fail(404, null, '未找到项目');
        }
        $this->appid = $this->lcic['appid'];
        $this->secretid = $this->lcic['secretid'];
        $this->secretkey = $this->lcic['secretkey'];
        $this->sdkappid = $this->lcic['sdkappid'];
        $this->end_point = $this->lcic['end_point'];
        try {
            if (!isset($this->cred)) {
                $this->cred = new Credential($this->secretid, $this->secretkey);
            }
            if (!isset($this->httpProfile)) {
                $this->httpProfile = new HttpProfile();
                $this->httpProfile->setEndpoint($this->end_point);
            }
            if (!isset($this->clientProfile)) {
                $this->clientProfile = new ClientProfile();
                $this->clientProfile->setHttpProfile($this->httpProfile);
            }
            if (!isset($this->client)) {
                $this->client = new LcicClient($this->cred, '', $this->clientProfile);
            }
        } catch (TencentCloudSDKException $e) {
            Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
        }
    }

    public function create()
    {
        $Name = \Input::Post("Name");
        $OriginId = \Input::Post("OriginId");
        $Avatar = \Input::Post("Avatar");
        $user = LcicUserModel::where("project", $this->token)->where("OriginId", $OriginId)->findOrEmpty();
        if ($user->isEmpty()) {
            try {
                $req = new RegisterUserRequest();

                $params = array(
                    'Name' => $Name,
                    'SdkAppId' => $this->sdkappid,
//                'OriginId' => $OriginId,
                    'Avatar' => $Avatar,
                );
                $req->fromJsonString(json_encode($params));
                $resp = $this->client->RegisterUser($req);

                // 输出json格式的字符串回包
//            print_r($resp->toJsonString());
                LcicUserModel::create([
                    'project' => $this->token,
                    'OriginId' => $OriginId,
                    'Name' => $Name,
                    'Avatar' => $Avatar,
                    'UserId' => $resp->getUserId(),
                    'Token' => $resp->getToken(),
                ]);
                Ret::Success(0, $resp, $resp->getToken());
            } catch (TencentCloudSDKException $e) {
                Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
            }
        } else {
            $this->modify();
        }

    }

    public function modify()
    {
        $Name = \Input::Post('Name');
        $OriginId = \Input::Post('OriginId');
        $Avatar = \Input::Post('Avatar');
        $UserId = LcicUserModel::where([
            "project" => $this->token,
            "OriginId" => $OriginId,
        ])->value("UserId");
        try {
            // 实例化一个请求对象,每个接口都会对应一个request对象
            $req = new ModifyUserProfileRequest();

            $params = array(
                'UserId' => $UserId,
                'Nickname' => $Name,
                'Avatar' => $Avatar,
            );
            $req->fromJsonString(json_encode($params));

            // 返回的resp是一个ModifyUserProfileResponse的实例，与请求对象对应
            $resp = $this->client->ModifyUserProfile($req);
            LcicUserModel::where([
                "project" => $this->token,
                "OriginId" => $OriginId,
            ])->update([
                'Name' => $Name,
                'Avatar' => $Avatar,
            ]);
            $user = LcicUserModel::where('project', $this->token)->where('OriginId', $OriginId)->column("UserId,Token");

            // 输出json格式的字符串回包
            Ret::Success(0, $user->toArray(), $user["Token"]);
        } catch (TencentCloudSDKException $e) {
            Ret::Fail(500, $e->getErrorCode(), $e->getMessage());
        }
    }


}