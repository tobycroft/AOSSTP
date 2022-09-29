<?php


use BaseController\CommonController;
use CreateImg\InfoCodePhoto;

class create extends CommonController
{

    public function index(Request $request)
    {
        $a = new InfoCodePhoto();
    }
}