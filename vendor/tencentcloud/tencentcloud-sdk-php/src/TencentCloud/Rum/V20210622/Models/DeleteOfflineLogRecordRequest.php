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
namespace TencentCloud\Rum\V20210622\Models;
use TencentCloud\Common\AbstractModel;

/**
 * DeleteOfflineLogRecord请求参数结构体
 *
 * @method string getProjectKey() 获取项目唯一上报 key
 * @method void setProjectKey(string $ProjectKey) 设置项目唯一上报 key
 * @method string getFileID() 获取离线日志文件 id
 * @method void setFileID(string $FileID) 设置离线日志文件 id
 */
class DeleteOfflineLogRecordRequest extends AbstractModel
{
    /**
     * @var string 项目唯一上报 key
     */
    public $ProjectKey;

    /**
     * @var string 离线日志文件 id
     */
    public $FileID;

    /**
     * @param string $ProjectKey 项目唯一上报 key
     * @param string $FileID 离线日志文件 id
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
        if (array_key_exists("ProjectKey",$param) and $param["ProjectKey"] !== null) {
            $this->ProjectKey = $param["ProjectKey"];
        }

        if (array_key_exists("FileID",$param) and $param["FileID"] !== null) {
            $this->FileID = $param["FileID"];
        }
    }
}
