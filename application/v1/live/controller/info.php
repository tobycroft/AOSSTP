<?php

namespace app\v1\live\controller;

use app\v1\image\controller\create;
use app\v1\live\action\GetUrl;
use app\v1\live\model\LiveAliyunModel;
use app\v1\live\model\LiveModel;
use app\v1\live\model\LiveTencentModel;
use Input;
use Ret;

class info extends create
{


    protected mixed $tencent;
    protected mixed $aliyun;

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
                $this->tencent = LiveTencentModel::where("tag", $this->live["tag"])->find();
                if (!$this->tencent) {
                    Ret::Fail(404, null, '未找到腾讯方案');
                }
                break;

            case "aliyun":
                $this->aliyun = LiveAliyunModel::where('tag', $this->live['tag'])->find();
                if (!$this->aliyun) {
                    Ret::Fail(404, null, '未找到阿里方案');
                }
                break;

            default:
                break;
        }
    }

    public function create()
    {
        $title = Input::Post("title");
        $datetime = date(DATE_RFC3339, time() + 86400);
        $url = GetUrl::getPushUrl($this->tencent["domain"], $title, $this->tencent["apikey"], $datetime);
        Ret::Success(0, $url);
    }

    public function create_all()
    {
        $title = Input::Post("title");
        $datetime = date(DATE_RFC3339, time() + 86400);
        $url = GetUrl::getAll($this->tencent["domain"], $this->tencent["play_domain"], $title, $this->tencent["apikey"], $this->tencent["play_key"], $datetime);
        Ret::Success(0, $url);
    }

    public function play_url()
    {
        $title = Input::Post('title');
        $datetime = date(DATE_RFC3339, time() + 86400);
        $url = GetUrl::getPlayUrl($this->tencent['play_domain'], $title, $this->tencent['play_key'], $datetime);
        Ret::Success(0, $url);
    }


}