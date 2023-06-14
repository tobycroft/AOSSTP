<?php

namespace app\v1\rsa\action;

class Rsa
{
    private static $PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAkYQwdEo3UAyBlUGdmfDbt1IiCm5/e1cVzkstle7mLmkGWSnG
LQbBxlAbKqh13jTcAr5PGlCt1vXyBBCi6UrOwBYcUeKehXNZ85/FCR6LG96cUCe6
LWkoo1Wr1xu1Wl8fTDqlqAkQar6CbZFAn1D7vNCsIqbPsOspdOVWL4YCnCIBLZFn
6HoL9XPBBIRhFH6Bg4FEam6A0Qx4EfoJczNpMKzTeGHphF0iqhn29bVd8nOUgVvb
mBBzftuVHeYwJJ62rRH05lvn4MWS/wAU+l6QZSONXgCTb6oW1LSSotnr1s6zTXUq
WAefHMz4S0labVS1/KzLiXslKw/jxoIMqGd9awIDAQABAoIBAAegGSVaVSBEvlx+
OXd0P7Pab7CDHB+nFAoFtoFBcnnnjeEJsIMpPTLaohHP6Pi9HGE6tYOcprZL4a0X
JGAObKTh8XMdoHNAGVoMDUqlC6v7WKoxfQpmcFK6RNptbwAFWcaDtWO+jLWv2ppu
sQMV9ZslGM3YDaxyAbj/mU7h8Sx/lSDT9E6nsKTr1YnF6Pt2owUWhVx8TmYWQ74Z
GRoE4kBWdN2jUBsYwRgygDlIz3eRb+IrzVCZ1CrFwuselBSbudza26VyOVtLI27W
bIqsJbvslIrG45llsEBbBKNAvFMpMrZ5iwy4iwnBXpFe0uIwihNX3UcQOSQtoRwZ
/LbwYUECgYEAzQxAh4+eZbyaDXal1CY6Kono+hdks2OrOz/9kPoqumU2aQh9YzGj
2QrfUGatVJJeAE1CHtb7JVIWE2c2aL02pEE9fD6i+f2x8FTWo600x5REZ4tUq4PK
rGa3FJ3j29w42E//wn32QJuYzdzIoRqFGGsUqAD05+8t+59wUQeJbiUCgYEAtazy
9BZdyeoOKpMQhde4OlQfpa5TBbsRRuNuLO5oSjh0aMTQrERDJ9C2fMnST1/YtT6P
VvaP4Y2+8t4ExjqyC/0R8S2hrRIxdk2XQgYP8O+J1iG6mD8COKdAKme5Lpzjn40H
kMmy6PCPox0rTPhAxb7Ff+Ad8yFPJ2fssvR8gE8CgYAaDMJt9nsDJdDUgQxURX8C
RH8KtsoeWD06y8hlEMM45v7gnSmA3p+YRV9VkZXXtrimhSovTNSfSyxuzqAE8nTs
rTUogbSm2eovRDf4l1qrOFTwaq0ZFSc8e9gqkFyQZAv8vz0Y8nPEhYAGN1Rt0zax
gXkgVu7GQIaw/vJ/+Nsm2QKBgBWLeRRxQpYbZ6qs3hzBRFvGdipTzgyz7oyVlA9I
Bp4mq8dw774+Kiiim8GYvZQkLbLwxFbvzohVIvvyUGaht1Oso2ASpUW9DpiVAwcV
aPaNsa7vOQ2tCzpkuztMKa3ZdTUKqIcHJGxzetVBNE1gc23//bP4hBS9MoHd1Tgz
vkAJAoGBALSk5XU5YCDgLtiATYZ9ABeHOnT7DGl6bof3gl75hkRxJacEWeBTJccR
hyK3ITV2mIP4mygcS6DDViW/bc/XJb6Gf12SVYlsA4gv5N2Wsk7N1oUcfT+YSrJC
Bz9nL+D3IDx5NGoowAzpTOfRUZEAlK3ZbmBHqVvXi+GKNiLummdB
-----END RSA PRIVATE KEY-----';
    private static $PUBLIC_KEY = 'rsa_public_key.pem 内容';

    /**
     * 创建签名
     * @param string $data 数据
     * @return null|string
     */
    public function createSign($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_sign(openssl_digest($data, 'sha256'), $sign, self::getPrivateKey(), OPENSSL_ALGO_SHA256) ? base64_encode($sign) : null;
    }

    public function sign($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_sign($data, $sign, self::getPrivateKey(), OPENSSL_ALGO_SHA256) ? base64_encode($sign) : null;
    }

    public function digest($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_digest($data, 'sha256');
    }


    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey()
    {
        $privKey = self::$PRIVATE_KEY;
        return openssl_pkey_get_private($privKey);
    }

    /**
     * 验证签名
     * @param string $data 数据
     * @param string $sign 签名
     * @return bool
     */
    public function verifySign($data = '', $sign = '')
    {
        if (!is_string($sign) || !is_string($sign)) {
            return false;
        }
        return (bool)openssl_verify(
            $data,
            base64_decode($sign),
            self::getPublicKey(),
            OPENSSL_ALGO_SHA256
        );
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $publicKey = self::$PUBLIC_KEY;
        return openssl_pkey_get_public($publicKey);
    }
}