<?php

class Input
{
    public static function Post(string $name, $must_have = true, bool $xss = false): string
    {
        if (!\think\facade\Request::has($name) && $must_have) {
            Ret::Fail(400, null, "Input-Post:" . $name);
        }
        $in = request()->post($name);
        if ($xss) {
            return removeXSS($in);
        } else {
            return $in;
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