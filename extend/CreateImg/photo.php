<?php

class InfoCodePhoto
{
    public static $data = [
        "title" => "杆子光伏安全定点巡检二维码",
        "title_font_size" => 35,
        "font_file" => ROOT_PATH . "public\\ttf\\" . "abd.ttf",
        "photo_width" => 1064,
        "photo_height" => 639,
        "data_line_height" => 80,
        "data_font_size" => 22,
        "data_start_x" => 40,
        "data_start_y" => 222,
        "data_col_distance" => 180,
        "line_number" => 12,
        "value" => [],
        "background" => [
            "background_color_height" => 160,
            "background_color_weight" => 1064,
            "background_color_red" => 49,
            "background_color_green" => 82,
            "background_color_blue" => 128,
            "background_color_clarity" => 0, // 透明度 0-127 0不透明
        ],
        "logo" => [
            "logo_position_x" => 60,
            "logo_position_y" => 27,
            "logo_width" => 80,
            "logo_height" => 107,
            "logo_path" => ROOT_PATH . "public\\ttf\\" . "logo.png",
        ],
        "qr_code" => [
            "logo_position_x" => 670,
            "logo_position_y" => 210,
            "logo_width" => 350,
            "logo_height" => 350,
            "logo_path" => "123.png",
        ],
    ];

    public function generate_photo($title, $value, $qr_local_save_path, $photo_local_save_path, $origin_data = [])
    {
        self::$data["title"] = $title;
        self::$data["value"] = $value;
        self::$data["qr_code"]["logo_path"] = $qr_local_save_path;
        foreach (self::$data as $key => $val) {
            if (array_key_exists($key, $origin_data)) {
                self::$data[$key] = $origin_data[$key];
            }
        }
        //创建画布
        $im = imagecreatetruecolor(self::$data["photo_width"], self::$data["photo_height"]);
        $color = imagecolorallocate($im, 255, 255, 255);
        // 填充背景
        imagefill($im, 0, 0, $color);
        // 填充标题背景
        $this->deal_background($im, self::$data);
        // logo
        if (key_exists("logo", self::$data)) {
            $this->deal_logo($im, self::$data["logo"]);
        }
        // 二维码
        if (key_exists("qr_code", self::$data)) {
            $this->deal_logo($im, self::$data["qr_code"]);
        }
        // 备注颜色
        $font_color_1 = ImageColorAllocate($im, 79, 79, 79);
        // 数据颜色
        $font_color_2 = ImageColorAllocate($im, 28, 28, 28);
        // 标题颜色
        $font_color_3 = ImageColorAllocate($im, 255, 255, 255);
        // 填写标题
        imagettftext($im, (int)self::$data["title_font_size"], 0, 235, 102, $font_color_3, self::$data["font_file"], self::$data["title"]);
        //温馨提示
        imagettftext($im, 15, 0, 680, 610, $font_color_1, self::$data["font_file"], '注：使用甘孜光伏APP扫码查看详情');
        // 填充数据
        $temp = false;
        $count = 1;
        foreach (self::$data["value"] as $key => $val) {
            $num = 0;
            if ($temp) {
                $cou = 2;
                $count = $count + 1;
            } else {
                $cou = 1;
            }
            if (strpos($val["title"], "设备编码") !== false) {
                $line_number = 8;
            } else {
                unset($line_number);
            }
            if (strpos($val["title"], "安装位置") !== false) {
                $row = 5;
            } else {
                $row = 2;
            }
            foreach ($val as $v) {
                $theTitle = $this->cn_row_substr($v, $row, $line_number ?? self::$data["line_number"]);
                if (!empty($theTitle[2])) {
                    $temp = true;
                } else {
                    $temp = false;
                }
                for ($i = 0; $i < $row; $i++) {
                    imagettftext($im, (int)self::$data["data_font_size"], 0, (int)(self::$data["data_start_x"] + $num * self::$data["data_col_distance"]), (int)(self::$data["data_start_y"] + (40 * $i) + 20 * $count + $key * self::$data["data_line_height"]), $font_color_2, self::$data["font_file"], $theTitle[$i + 1]);
                }
                // imagettftext($im, (int) self::$data["data_font_size"], 0, (int) (self::$data["data_start_x"] + $num * self::$data["data_col_distance"]), (int) (self::$data["data_start_y"] + 20 * $count + $key * self::$data["data_line_height"]), $font_color_2, self::$data["font_file"], $theTitle[1]);
                // imagettftext($im, (int) self::$data["data_font_size"], 0, (int) (self::$data["data_start_x"] + $num * self::$data["data_col_distance"]), (int) (self::$data["data_start_y"] + 40 + 20 * $count + $key * self::$data["data_line_height"]), $font_color_2, self::$data["font_file"], $theTitle[2]);
                $num = $num + 1;
            }
        }

        //输出图片
        if (self::$data["qr_code"]["logo_path"]) {
            imagepng($im, $photo_local_save_path);
        } else {
            Header("Content-Type: image/jpg");
            imagepng($im);
        }
        //释放空间
        imagedestroy($im);
    }

    public function deal_background($im, $data)
    {
        if (key_exists("background", $data)) {
            $image_color = imagecolorallocatealpha($im, $data["background"]["background_color_red"], $data["background"]["background_color_green"], $data["background"]["background_color_blue"], $data["background"]["background_color_clarity"]);
            imagefilledrectangle($im, 0, 0, $data["background"]["background_color_weight"], $data["background"]["background_color_height"], $image_color);
        }
    }

    public function deal_logo($im, $data)
    {
        list($code_w, $code_h) = getimagesize($data["logo_path"]);
        $codeImg = $this->createImageFromFile($data["logo_path"]);
        if (!is_null($codeImg)) {
            imagecopyresized($im, $codeImg, $data["logo_position_x"], $data["logo_position_y"], 0, 0, $data["logo_width"], $data["logo_height"], $code_w, $code_h);
        }
        //释放空间
        imagedestroy($codeImg);
    }

    /**
     * 从图片文件创建Image资源
     * @param $file 图片文件，支持url
     * @return bool|\GdImage    成功返回图片image资源，失败返回false
     */
    public function createImageFromFile($file)
    {
        if (preg_match('/http(s)?:\/\//', $file)) {
            $fileSuffix = $this->getNetworkImgType($file);
        } else {
            // $fileSuffix = pathinfo($file, PATHINFO_EXTENSION);
            $img_data = getimagesize($file);
            $fileSuffix = $img_data["mime"];
        }

        if (!$fileSuffix) {
            return false;
        }

        switch ($fileSuffix) {
            case 'jpeg':
                $theImage = @imagecreatefromjpeg($file);
                break;
            case 'jpg':
                $theImage = @imagecreatefromjpeg($file);
                break;
            case 'png':
                $theImage = @imagecreatefrompng($file);
                break;
            case 'gif':
                $theImage = @imagecreatefromgif($file);
                break;
            default:
                $theImage = @imagecreatefromstring(file_get_contents($file));
                break;
        }

        return $theImage;
    }

    /**
     * 获取网络图片类型
     * @param $url  网络图片url,支持不带后缀名url
     * @return bool
     */
    public function getNetworkImgType($url)
    {
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); //设置需要获取的URL
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //支持https
        curl_exec($ch); //执行curl会话
        $http_code = curl_getinfo($ch); //获取curl连接资源句柄信息
        curl_close($ch); //关闭资源连接

        if ($http_code['http_code'] == 200) {
            $theImgType = explode('/', $http_code['content_type']);

            if ($theImgType[0] == 'image') {
                return $theImgType[1];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 分行连续截取字符串
     * @param $str  需要截取的字符串,UTF-8
     * @param int $row 截取的行数
     * @param int $number 每行截取的字数，中文长度
     * @param bool $suffix 最后行是否添加‘...’后缀
     * @return array    返回数组共$row个元素，下标1到$row
     */
    public function cn_row_substr($str, $row = 1, $number = 10, $suffix = true)
    {
        $result = array();
        for ($r = 1; $r <= $row; $r++) {
            $result[$r] = '';
        }

        $str = trim($str);
        if (!$str) {
            return $result;
        }

        $theStrlen = strlen($str);

        //每行实际字节长度
        $oneRowNum = $number * 3;
        for ($r = 1; $r <= $row; $r++) {
            if ($r == $row and $theStrlen > $r * $oneRowNum and $suffix) {
                $result[$r] = $this->mg_cn_substr($str, $oneRowNum - 6, ($r - 1) * $oneRowNum) . '...';
            } else {
                $result[$r] = $this->mg_cn_substr($str, $oneRowNum, ($r - 1) * $oneRowNum);
            }
            if ($theStrlen < $r * $oneRowNum) {
                break;
            }

        }

        return $result;
    }

    /**
     * 按字节截取utf-8字符串
     * 识别汉字全角符号，全角中文3个字节，半角英文1个字节
     * @param $str  需要切取的字符串
     * @param $len  截取长度[字节]
     * @param int $start 截取开始位置，默认0
     * @return string
     */
    public function mg_cn_substr($str, $len, $start = 0)
    {
        $q_str = '';
        $q_strlen = ($start + $len) > strlen($str) ? strlen($str) : ($start + $len);

        //如果start不为起始位置，若起始位置为乱码就按照UTF-8编码获取新start
        if ($start and json_encode(substr($str, $start, 1)) === false) {
            for ($a = 0; $a < 3; $a++) {
                $new_start = $start + $a;
                $m_str = substr($str, $new_start, 3);
                if (json_encode($m_str) !== false) {
                    $start = $new_start;
                    break;
                }
            }
        }

        //切取内容
        for ($i = $start; $i < $q_strlen; $i++) {
            //ord()函数取得substr()的第一个字符的ASCII码，如果大于0xa0的话则是中文字符
            if (ord(substr($str, $i, 1)) > 0xa0) {
                $q_str .= substr($str, $i, 3);
                $i += 2;
            } else {
                $q_str .= substr($str, $i, 1);
            }
        }
        return $q_str;
    }
}
