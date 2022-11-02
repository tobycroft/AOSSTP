<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\logger\model;


use think\Model;

class LoggerVisitModel extends Model
{

    public $table = 'ao_logger_visit';

    public function Api_insert($project, $ip, $host, $path, $header, $request, $change_date)
    {
        $red = new \Redis();
        self::insert([
            "project" => $project,
            "ip" => $ip,
            "host" => $host,
            "path" => $path,
            "header" => $header,
            "request" => $request,
            "change_date" => $change_date,
        ]);
    }

    public function Api_insert_all($project, $log, $discript)
    {
        self::insertAll();
    }

}
