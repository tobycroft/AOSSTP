<?php

namespace app\v1\hook\controller;

use app\v1\hook\action\HookAction;
use app\v1\hook\model\HookModel;

class push
{
    public function single()
    {
        $tag = \Input::Get("tag");
        $data = HookModel::where("tag", $tag)->getData();
        if ($data) {
            $rets = [];
            $status = [];
            foreach ($data as $datum) {
                switch ($data['mode']) {
                    case 'aapanel':
                        $path = $data['method'] . '://' . $data['domain'] . '/hook';
                        $query = [
                            'access_key' => $data['key'],
                        ];
                        $ret = HookAction::raw_post($path, $query);
                        $rets[$data['remark']] = $ret;
                        if ($ret) {
                            $status[$data["remark"]] = "success";
                        } else {
                            $status[$data['remark']] = 'fail';
                        }
                        break;

                    default:
                        $ret = HookAction::raw_post($data['url']);
                        $rets[$data['remark']] = $ret;
                        if ($ret) {
                            $status[$data['remark']] = 'success';
                        } else {
                            $status[$data['remark']] = 'fail';
                        }
                        break;
                }
                \Ret::Success(0, $rets);
            }
        } else {
            \Ret::Fail(404, null, "未找到项目");
        }
    }

    public function github()
    {
        $tag = \Input::Get("tag");
        $data = HookModel::where("tag", $tag)->getData();
        if ($data) {
            switch ($data["mode"]) {
                case "aapanel":
                    $path = $data["method"] . "://" . $data["domain"] . "/hook";
                    $query = [
                        "access_key" => $data["key"],
                    ];
                    $ret = HookAction::raw_post($path, $query);
                    if ($ret) {
                        \Ret::Success(0, $ret);
                    } else {
                        \Ret::Fail(200, $ret);
                    }
                    break;

                default:
                    $ret = HookAction::raw_post($data['url']);
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