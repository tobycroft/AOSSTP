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
 * ChangeInstanceRole请求参数结构体
 *
 * @method string getGroupId() 获取复制组ID
 * @method void setGroupId(string $GroupId) 设置复制组ID
 * @method string getInstanceId() 获取实例ID
 * @method void setInstanceId(string $InstanceId) 设置实例ID
 * @method string getInstanceRole() 获取实例角色，rw可读写，r只读
 * @method void setInstanceRole(string $InstanceRole) 设置实例角色，rw可读写，r只读
 */
class ChangeInstanceRoleRequest extends AbstractModel
{
    /**
     * @var string 复制组ID
     */
    public $GroupId;

    /**
     * @var string 实例ID
     */
    public $InstanceId;

    /**
     * @var string 实例角色，rw可读写，r只读
     */
    public $InstanceRole;

    /**
     * @param string $GroupId 复制组ID
     * @param string $InstanceId 实例ID
     * @param string $InstanceRole 实例角色，rw可读写，r只读
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
        if (array_key_exists("GroupId",$param) and $param["GroupId"] !== null) {
            $this->GroupId = $param["GroupId"];
        }

        if (array_key_exists("InstanceId",$param) and $param["InstanceId"] !== null) {
            $this->InstanceId = $param["InstanceId"];
        }

        if (array_key_exists("InstanceRole",$param) and $param["InstanceRole"] !== null) {
            $this->InstanceRole = $param["InstanceRole"];
        }
    }
}
