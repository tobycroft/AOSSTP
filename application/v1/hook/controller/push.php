<?php

namespace app\v1\hook\controller;

use app\v1\hook\model\HookModel;
use function Wechat\raw_post;

class push
{
    public function single()
    {
        $tag = \Input::Post("tag");
        $data = HookModel::where("tag", $tag)->findOrEmpty();
        if ($data) {
            switch ($data["mode"]) {
                case "aapanel":
                    $path = $data["method"] . "://" . $data["domain"] . "/hook?access_key=" . $data["key"];
                    $query = [
                        "access_key" => $data["key"],
                    ];
                    $ret = raw_post($path, $query,);
                    if ($ret) {
                        \Ret::Success(0, $ret);
                    } else {
                        \Ret::Fail(200, $ret);
                    }
                    break;

                default:
                    $ret = raw_post($data['url']);
                    if ($ret) {
                        \Ret::Success(0, $ret);
                    } else {
                        \Ret::Fail(200, $ret);
                    }
                    break;
            }
        } else {
            \Ret::Fail(404, null, "未找到项目");
        }
    }
}