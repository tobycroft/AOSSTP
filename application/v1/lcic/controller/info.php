<?php

namespace app\v1\lcic\controller;

use app\v1\image\controller\create;
use app\v1\lcic\model\LcicModel;
use Ret;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Lcic\V20220817\LcicClient;
use TencentCloud\Lcic\V20220817\Models\RegisterUserRequest;
use think\Request;

class info extends create
{

    public string|null $appid;
    public string|null $secretid;
    public string|null $secretkey;

    public string|null $access_token;

    protected mixed $wechat;


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
        $this->access_token = $this->wechat['access_token'];
    }

    public function create(Request $request)
    {
        try {
            // 实例化一个认证对象，入参需要传入腾讯云账户 SecretId 和 SecretKey，此处还需注意密钥对的保密
            // 代码泄露可能会导致 SecretId 和 SecretKey 泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议采用更安全的方式来使用密钥，请参见：https://cloud.tencent.com/document/product/1278/85305
            // 密钥可前往官网控制台 https://console.cloud.tencent.com/cam/capi 进行获取
            $cred = new Credential($this->secretid, $this->secretkey);
            // 实例化一个http选项，可选的，没有特殊需求可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint('lcic.tencentcloudapi.com');

            // 实例化一个client选项，可选的，没有特殊需求可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            // 实例化要请求产品的client对象,clientProfile是可选的
            $client = new LcicClient($cred, '', $clientProfile);

            // 实例化一个请求对象,每个接口都会对应一个request对象
            $req = new RegisterUserRequest();

            $params = array(
                'Name' => '1'
            );
            $req->fromJsonString(json_encode($params));

            // 返回的resp是一个RegisterUserResponse的实例，与请求对象对应
            $resp = $client->RegisterUser($req);

            // 输出json格式的字符串回包
            print_r($resp->toJsonString());
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }


}