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
namespace TencentCloud\Intlpartnersmgt\V20220928\Models;
use TencentCloud\Common\AbstractModel;

/**
 * AllocateCustomerCredit请求参数结构体
 *
 * @method float getAddedCredit() 获取分配客户信用的具体值
 * @method void setAddedCredit(float $AddedCredit) 设置分配客户信用的具体值
 * @method integer getClientUin() 获取客户uin
 * @method void setClientUin(integer $ClientUin) 设置客户uin
 */
class AllocateCustomerCreditRequest extends AbstractModel
{
    /**
     * @var float 分配客户信用的具体值
     */
    public $AddedCredit;

    /**
     * @var integer 客户uin
     */
    public $ClientUin;

    /**
     * @param float $AddedCredit 分配客户信用的具体值
     * @param integer $ClientUin 客户uin
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
        if (array_key_exists("AddedCredit",$param) and $param["AddedCredit"] !== null) {
            $this->AddedCredit = $param["AddedCredit"];
        }

        if (array_key_exists("ClientUin",$param) and $param["ClientUin"] !== null) {
            $this->ClientUin = $param["ClientUin"];
        }
    }
}
