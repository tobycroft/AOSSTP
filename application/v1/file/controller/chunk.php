<?php

namespace app\dashboard\controller;


use BaseController\CommonController;

class chunk extends CommonController
{


    /*
     * array(7) {
      ["id"] =&gt; string(9) "WU_FILE_0"
      ["name"] =&gt; string(12) "AUDIO002.WAV"
      ["type"] =&gt; string(9) "audio/wav"
      ["lastModifiedDate"] =&gt; string(54) "Sat Apr 21 2018 01:05:14 GMT+0800 (中国标准时间)"
      ["size"] =&gt; string(9) "191905506"
      ["chunks"] =&gt; string(2) "19"
      ["chunk"] =&gt; string(2) "18"
      }
     */

    public function upload()
    {
        $file = request()->file('file');
        switch (input('post.type')) {
            case 'video/mp4':
                break;
            case 'video/x-flv':
                break;
            case 'video/x-msvideo':
                break;
            case 'video/x-flv':
                break;
            case 'video/3gpp':
                break;
            case 'video/quicktime':
                break;
            case 'video/vnd.rn-realvideo':
                break;
            case 'video/mpeg':
                break;
            default:
                $end = explode('.', input('post.name', '', 'strip_tags'));
                switch (end($end)) {
                    case 'flv':
                        break;
                    case 'avi':
                        break;
                    case 'mkv':
                        break;
                    case 'wmv':
                        break;
                    case 'rmvb':
                        break;
                    case 'rm':
                        break;
                    default:
                        \RET::fail('不支持的文件类型' . end($end), 405);
                        break;
                }
                break;
        }
        if ($file) {
            if (input('size') > 4 * 1024 * 1204 * 1024) {
                \RET::fail('视频太大了，请不要上传超过4G的视频呢~', 407);
            }
            $directory = md5(session('uid') . '_' . input('size'));
            $name = input('name');
            $ext = explode('.', $name);
            if (!input('chunks')) {
                $pathname = config('app.video-combine-path');
                if (AcVideoTranscodeModel::api_find_size(session('uid'), input('size'))) {
                    \RET::fail('你已经上传过这个视频拉~', 403);
                } else {
                    $info = $file->move($pathname, $directory);
                    if ($info) {
                        AcVideoTranscodeModel::api_insert(session('uid'), $name, '0', '0', '0', input('size'), $directory, '1');
                        \think\Cache::clear('file_' . $directory);
                        \RET::success('上传成功');
                    } else {
                        \RET::fail('上传失败');
                    }
                }
            } else {
                $pathname = config('app.video-upload-path');
                if (AcVideoTranscodeModel::api_find_size(session('uid'), input('size'))) {
                    \RET::fail('你已经上传过这个视频拉~', 403);
                } else {
                    if (cache('file_' . $directory) == NULL) {
                        cache('file_' . $directory, [], 600);
                    }
                    if (file_exists($pathname . DS . $directory . DS . $directory . '_' . input('chunk') . '.' . end($ext))) {
                        $arr = cache('file_' . $directory);
                        $arr[input('chunk')] = true;
                        cache('file_' . $directory, $arr, 600);
                        \RET::success('分块文件已上传自动忽略');
                    }
                    $chunks = input('chunks');
                    $info = $file->move($pathname . DS . $directory, $directory . '_' . input('chunk'));
                    if ($info) {
                        if (count(cache('file_' . $directory)) >= ($chunks - 1)) {
                            AcVideoTranscodeModel::api_insert(session('uid'), $name, $chunks, '0', ($chunks - 1), input('size'), $directory);
                            cache('file_' . $directory, false, 1);
                            \RET::success('上传成功');
                        } else {
                            $arr = cache('file_' . $directory);
                            $arr[input('chunk')] = true;
                            cache('file_' . $directory, $arr, 600);
                            \RET::success(count(cache('file_' . $directory)) . '分块文件已收到' . input('chunk'));
                        }
                    } else {
                        \RET::fail('上传失败');
                    }
                }
            }
        } else {
            \RET::fail('nofile');
        }
    }

    public function isupload()
    {
        $pathname = config('app.video-upload-path');
        $directory = md5(session('uid') . '_' . input('size'));
        $name = input('name');
        $ext = explode('.', $name);
        if (file_exists($pathname . DS . $directory . DS . $directory . '_' . input('chunk') . '.' . end($ext))) {
            $chunks = input('chunks');
            if (count(cache('file_' . $directory)) >= ($chunks - 1)) {
                \RET::success(input('chunk') . '块可以上传');
            }
            if (cache('file_' . $directory) == NULL) {
                cache('file_' . $directory, [], 600);
            }
            $arr = cache('file_' . $directory);
            $arr[input('chunk')] = true;
            cache('file_' . $directory, $arr, 600);
            \RET::success(input('chunk') . '分块文件已上传自动忽略', -1);
        } else {
            \RET::success(input('chunk') . '块可以上传');
        }
    }

}
