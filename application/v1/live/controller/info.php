<?php

namespace app\v1\txlive\controller;

use app\v1\image\controller\create;
use app\v1\live\action\GetPushUrl;
use app\v1\live\model\LiveModel;
use app\v1\live\model\LiveTencentModel;
use Ret;

class info extends create
{


    protected mixed $tencent;

    protected mixed $live;


    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->live = LiveModel::where('project', $this->token)->find();
        if (!$this->live) {
            Ret::Fail(404, null, '未找到项目');
        }
        switch ($this->live["platform"]) {
            case "tencent":
                $this->tencent = LiveTencentModel::where("tag", $this->live["platform"]);
                if (!$this->tencent) {
                    Ret::Fail(404, null, '未找到腾讯模版');
                }
                break;

            default:
                break;
        }
    }

    public function create()
    {
        $title = \Input::Post("title");
        $url = GetPushUrl::getPushUrl($this->tencent["domain"], $title, $this->tencent["apikey"]);
        Ret::Success(0, $url);
    }


}