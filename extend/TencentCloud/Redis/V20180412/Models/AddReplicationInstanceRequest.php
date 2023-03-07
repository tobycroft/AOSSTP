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
 * AddReplicationInstance请求参数结构体
 *
 * @method string getGroupId() 获取复制组ID。
 * @method void setGroupId(string $GroupId) 设置复制组ID。
 * @method string getInstanceId() 获取实例ID。
 * @method void setInstanceId(string $InstanceId) 设置实例ID。
 * @method string getInstanceRole() 获取给复制组添加的实例分配角色。<ul><li>rw：可读写。</li><li>r：只读。</li></ul>
 * @method void setInstanceRole(string $InstanceRole) 设置给复制组添加的实例分配角色。<ul><li>rw：可读写。</li><li>r：只读。</li></ul>
 */
class AddReplicationInstanceRequest extends AbstractModel
{
    /**
     * @var string 复制组ID。
     */
    public $GroupId;

    /**
     * @var string 实例ID。
     */
    public $InstanceId;

    /**
     * @var string 给复制组添加的实例分配角色。<ul><li>rw：可读写。</li><li>r：只读。</li></ul>
     */
    public $InstanceRole;

    /**
     * @param string $GroupId 复制组ID。
     * @param string $InstanceId 实例ID。
     * @param string $InstanceRole 给复制组添加的实例分配角色。<ul><li>rw：可读写。</li><li>r：只读。</li></ul>
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
        if (array_key_exists("GroupId", $param) and $param["GroupId"] !== null) {
            $this->GroupId = $param["GroupId"];
        }

        if (array_key_exists("InstanceId", $param) and $param["InstanceId"] !== null) {
            $this->InstanceId = $param["InstanceId"];
        }

        if (array_key_exists("InstanceRole", $param) and $param["InstanceRole"] !== null) {
            $this->InstanceRole = $param["InstanceRole"];
        }
    }
}
