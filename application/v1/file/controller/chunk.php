<?php


namespace app\v1\file\controller;


use app\v1\project\model\ProjectModel;

class chunk extends dp
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

    public function upload_chunk($dir = '', $from = '', $module = '')
    {
        $token = $this->token;
        $proc = ProjectModel::api_find_token($token);
        if (!$proc) {
            return $this->uploadError($from, "项目不可用");
        }
        $file = request()->file('file');
        if ($file) {
            $name = input('name');
            $ext = explode('.', $name);
            $file_ident = md5(session('uid') . '_' . input('size'));

            if (cache('file_' . $file_ident) == NULL) {
                cache('file_' . $file_ident, [], 600);
            }
            if (file_exists('./upload/' . $this->token . DIRECTORY_SEPARATOR . $file_ident . DIRECTORY_SEPARATOR . $file_ident . '_' . input('chunk') . '.' . end($ext))) {
                $arr = cache('file_' . $file_ident);
                $arr[input('chunk')] = true;
                cache('file_' . $file_ident, $arr, 600);
                \RET::success('分块文件已上传自动忽略');
            }
            $chunks = input('chunks');
            $info = $file->move('./upload/' . $this->token, $file_ident . '_' . input('chunk'));
            if ($info) {
                if (count(cache('file_' . $file_ident)) >= ($chunks - 1)) {
                    AcVideoTranscodeModel::api_insert(session('uid'), $name, $chunks, '0', ($chunks - 1), input('size'), $file_ident);
                    cache('file_' . $file_ident, false, 1);
                    \RET::success('上传成功');
                } else {
                    $arr = cache('file_' . $file_ident);
                    $arr[input('chunk')] = true;
                    cache('file_' . $file_ident, $arr, 600);
                    \RET::success(count(cache('file_' . $file_ident)) . '分块文件已收到' . input('chunk'));
                }
            } else {
                \RET::fail('上传失败');
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
