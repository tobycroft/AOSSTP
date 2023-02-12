<?php

namespace app\v1\hook\controller;

use app\v1\hook\action\HookAction;
use app\v1\hook\model\HookModel;

class push
{
    public function single()
    {
        $tag = \Input::Get("tag");
        $data = HookModel::where("tag", $tag)->select();
        if ($data) {
            $rets = [];
            $status = [];
            foreach ($data as $datum) {
                switch ($datum['mode']) {
                    case 'aapanel':
                        $path = $datum['method'] . '://' . $datum['domain'] . '/hook';
                        $query = [
                            'access_key' => $datum['key'],
                        ];
                        $ret = HookAction::raw_post($path, $query);
                        $rets[$datum['remark']] = $ret;
                        if ($ret) {
                            $status[$datum["remark"]] = "success";
                        } else {
                            $status[$datum['remark']] = 'fail';
                        }
                        break;

                    default:
                        $ret = HookAction::raw_post($datum['url']);
                        $rets[$datum['remark']] = $ret;
                        if ($ret) {
                            $status[$datum['remark']] = 'success';
                        } else {
                            $status[$datum['remark']] = 'fail';
                        }
                        break;
                }
                \Ret::Success(0, $rets);
            }
        } else {
            \Ret::Fail(404, null, "未找到项目");
        }
    }

    //http://upload.tuuz.cc:8000/v1/hook/push/github
    public function github()
    {
        $in = \Input::Post('payload');
        $payload = json_decode($in, 1);
        if (!isset($payload['repository']['name'])) {
            \Ret::Fail(400, null, "未找到repository-name字段");
        }
        $data = HookModel::where('tag', $payload["repository"]["name"])->select();
        if ($data) {
            $rets = [];
            $status = [];
            foreach ($data as $datum) {
                switch ($datum['mode']) {
                    case 'aapanel':
                        $path = $datum['method'] . '://' . $datum['domain'] . '/hook';
                        $query = [
                            'access_key' => $datum['key'],
                        ];
                        $ret = HookAction::raw_post($path, $query);
                        $rets[$datum['remark']] = $ret;
                        if ($ret) {
                            $status[$datum['remark']] = 'success';
                        } else {
                            $status[$datum['remark']] = 'fail';
                        }
                        break;

                    default:
                        $ret = HookAction::raw_post($datum['url']);
                        $rets[$datum['remark']] = $ret;
                        if ($ret) {
                            $status[$datum['remark']] = 'success';
                        } else {
                            $status[$datum['remark']] = 'fail';
                        }
                        break;
                }
                \Ret::Success(0, $rets);
            }
        } else {
            \Ret::Fail(404, null, '未找到项目');
        }
    }
}