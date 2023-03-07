<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Redis\V20180412\Models;
use TencentCloud\Common\AbstractModel;

/**
 * 实例安全组信息
 *
 * @method string getInstanceId() 获取实例Id
 * @method void setInstanceId(string $InstanceId) 设置实例Id
 * @method array getSecurityGroupDetails() 获取安全组信息
 * @method void setSecurityGroupDetails(array $SecurityGroupDetails) 设置安全组信息
 */
class InstanceSecurityGroupDetail extends AbstractModel
{
    /**
     * @var string 实例Id
     */
    public $InstanceId;

    /**
     * @var array 安全组信息
     */
    public $SecurityGroupDetails;

    /**
     * @param string $InstanceId 实例Id
     * @param array $SecurityGroupDetails 安全组信息
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("InstanceId",$param) and $param["InstanceId"] !== null) {
            $this->InstanceId = $param["InstanceId"];
        }

        if (array_key_exists("SecurityGroupDetails",$param) and $param["SecurityGroupDetails"] !== null) {
            $this->SecurityGroupDetails = [];
            foreach ($param["SecurityGroupDetails"] as $key => $value){
                $obj = new SecurityGroupDetail();
                $obj->deserialize($value);
                array_push($this->SecurityGroupDetails, $obj);
            }
        }
    }
}
