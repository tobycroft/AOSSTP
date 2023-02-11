<?php

use think\facade\Request;

class Input
{
    public static function Post(string $name, bool $must_have = true, bool $xss = false): string
    {
        if (!Request::has($name) && $must_have) {
            Ret::Fail(400, null, "Input-Post:[" . $name . "]");
        }
        $in = strval(request()->post($name, '', 'strip_tags'));
        if ($xss) {
            return removeXSS($in);
        } else {
            return $in;
        }
    }

    public static function PostFloat(string $name, bool $must_have = true): float
    {
        if (!Request::has($name . "/f") && $must_have) {
            Ret::Fail(400, null, "Input-Post-Float:[" . $name . "]");
        }
        $in = floatval(request()->post($name . '/f'));
        if ($in) {
            return $in;
        } else {
            return 0;
        }
    }

    public static function PostBool(string $name, bool $must_have = true): bool
    {
        if (!Request::has($name . "/b") && $must_have) {
            Ret::Fail(400, null, "Input-Post-Bool:[" . $name . "]");
        }
        $in = boolval(request()->post($name . '/b'));
        if ($in) {
            return $in;
        }
        return false;
    }

    public static function PostInt(string $name, bool $must_have = true): int
    {
//        echo Request::post($name);
        echo Request::has($name . '/d');
        if (!Request::has($name) && $must_have) {
            Ret::Fail(400, null, "Input-Post-Int:[" . $name . "]");
        }
        $in = intval(request()->post($name . '/d'));
        if ($in) {
            return $in;
        } else {
            Ret::Fail(400, null, 'Input-Post-Json:[' . $name . '] should be json string');
        }
        return 0;
    }

    public static function PostJson(string $name, bool $must_have = true): array
    {
        if (!Request::has($name) && $must_have) {
            Ret::Fail(400, null, 'Input-Post-Json:[' . $name . ']');
        }
        $in = strval(request()->post($name));
        if ($json = json_decode($in, true)) {
            return $json;
        } else {
            Ret::Fail(400, null, 'Input-Post-Json:[' . $name . '] should be json string');
            return [];
        }
    }

}

function removeXSS($data)
{

    $_clean_xss_config = HTMLPurifier_Config::createDefault();
    $_clean_xss_config->set('Core.Encoding', 'UTF-8');
    // 保留的标签
    $_clean_xss_config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]');
    $_clean_xss_config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
    $_clean_xss_config->set('HTML.TargetBlank', TRUE);
    $_clean_xss_obj = new HTMLPurifier($_clean_xss_config);
    return $_clean_xss_obj->purify($data);

}