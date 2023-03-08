<?php

namespace app\v1\lcic\controller;

use app\v1\image\controller\create;
use app\v1\lcic\model\LcicModel;
use Ret;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Lcic\V20220817\Models\BatchRegisterRequest;
use TencentCloud\Lcic\V20220817\Models\ModifyUserProfileRequest;
use TencentCloud\Lcic\V20220817\Models\RegisterUserRequest;


class user extends create
{

    public string|null $appid;
    public string|null $secretid;
    public string|null $secretkey;

    public int|null $sdkappid;

    protected mixed $wechat;

    protected mixed $cred;
    protected mixed $httpProfile;
    protected mixed $clientProfile;

    protected mixed $client;


    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->wechat = LcicModel::where('project', $this->token)->find();
        if (!$this->wechat) {
            Ret::Fail(404, null, '未找到项目');
        }
        $this->appid = $this->wechat['appid'];
        $this->secretid = $this->wechat['secretid'];
        $this->secretkey = $this->wechat['secretkey'];
        $this->sdkappid = $this->wechat['sdkappid'];
        try {
            if (!isset($this->cred)) {
                $this->cred = new Credential($this->secretid, $this->secretkey);
            }
            if (!isset($this->httpProfile)) {
                $this->httpProfile = new HttpProfile();
                $this->httpProfile->setEndpoint('lcic.tencentcloudapi.com');
            }
            if (!isset($this->clientProfile)) {
                $clientProfile = new ClientProfile();
                $clientProfile->setHttpProfile($this->httpProfile);
            }
            if (!isset($this->client)) {
//                $this->client = new LcicClient($this->cred, '', $this->clientProfile);
            }
        } catch (TencentCloudSDKException $e) {
            Ret::Fail($e->getCode(), $e->getErrorCode(), $e->getMessage());
        }
    }

    public function create()
    {
        $Name = \Input::Post("Name");
        $OriginId = \Input::Post("OriginId");
        $Avatar = \Input::Post("Avatar");
        try {
            $req = new RegisterUserRequest();

            $params = array(
                'Name' => $Name,
                'SdkAppId' => $this->sdkappid,
                'OriginId' => $OriginId,
                'Avatar' => $Avatar,
            );
            $req->fromJsonString(json_encode($params));
            $resp = $this->client->RegisterUser($req);

            // 输出json格式的字符串回包
//            print_r($resp->toJsonString());
            Ret::Success(0, $resp->toJsonString(), $resp->getToken());
        } catch (TencentCloudSDKException $e) {
            Ret::Fail($e->getCode(), $e->getErrorCode(), $e->getMessage());
        }
    }

    public function modify()
    {
        $Name = \Input::Post('Name');
        $UserId = \Input::Post('UserId');
        $Avatar = \Input::Post('Avatar');
        try {
            // 实例化一个请求对象,每个接口都会对应一个request对象
            $req = new ModifyUserProfileRequest();

            $params = array(
                'UserId' => $UserId,
                'Nickname' => $Name,
                'Avatar' => $Avatar
            );
            $req->fromJsonString(json_encode($params));

            // 返回的resp是一个ModifyUserProfileResponse的实例，与请求对象对应
            $resp = $this->client->ModifyUserProfile($req);

            // 输出json格式的字符串回包
            print_r($resp->toJsonString());
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }

    public function create_more()
    {
        $Name = \Input::Post("Name");
        $SdkAppId = \Input::PostInt("SdkAppId");
        $OriginId = \Input::Post("OriginId");
        $Avatar = \Input::Post("Avatar");
        try {
            $req = new BatchRegisterRequest();

            $params = array(
                'Users' => array(
                    array(
                        'SdkAppId' => 3471043,
                        'Name' => 'test',
                        'OriginId' => '1',
                        'Avatar' => '1'
                    ),
                    array(
                        'SdkAppId' => 3471043,
                        'Name' => 'test',
                        'OriginId' => '1',
                        'Avatar' => '1'
                    )
                )
            );
            $req->fromJsonString(json_encode($params));

            // 返回的resp是一个BatchRegisterResponse的实例，与请求对象对应
            $resp = $this->client->BatchRegister($req);

            // 输出json格式的字符串回包
            print_r($resp->toJsonString());
            $resp->getUsers();
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }


}