<?php

namespace app\v1\image\controller;


use sezaicetin\Create\img;

class create
{

    public function index()
    {
        header("Content-Type: image/png", true);
        $img = new img("test");
        echo "<img src='" . $img->create(200, 200) . "'>";
    }

}