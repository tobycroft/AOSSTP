<?php

namespace app\v1\rsa\action;

class Rsa
{
    private static $PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCr8KAg7d7oSVXCH1S4xrxNrqKPD5NrQSdqHES/21Onq4zsDBHm2K56YuO4FlI2HfMYzEimTdImKYPRXoX/7XKnP110D6x5R23KiGeojlo6EagKUgDWqVlGYCOJtTKltRqOq1YIp5/48gSfn+vVDXpP1BRWoxIWi9yKt/7nKHkYTcqVsqYJCzgpg0qxTjzUMOXyFypdeAIavxzmvCXd5iruWD65Z6per/4JekvD4dOLDq7MX1whcz/J6VHCsYSm40MBDk7XmiNMLl1xh8CEu4r4JS7fw/PH8Skc7/VUpwiUtf4FIVi8v8FlY4wxtNkDNZMovlPQkF36nuk+fZElSPa1AgMBAAECggEBAKc1rSQY8GxJ1UKhoYzIiJurN5+U8DKf/CqvLxS9wQ8QQE3WNxqeop+WyApgerbyTVVSZVyF1dcTDIRyPjJzwspvTvztuTeAhX8vRE+hkJFkVl0i/nnD23QbTrQ/KPAJvbf9W/+ytFovUtvBdGGPZ+F2SAkxMQgIIAQvh49ZxtogRKT/v35hw5xJ/x7jhmM3SjK8B0ULP4rgc/n9ddOHTWI+qV30aZAe5jsPiyj2E5D20j+MPXPWwHW5l/5eVSRZVHpc6Z8+E5yXb6svlnFAUgHGFHCAoXPbgvm/JAmJIcXUnb56ElpVMUSrK1h0VOgOPStvkRvbixmKQzzJBDiCxEkCgYEA1/Wtb+vha5Or3p2t0X/z4nr50cwft2TtwXzUfD13XE2EnKL52hc8VVK9bON+ifqwiW5rjXAlX5wTy2gEP/9MgnOW3jYf2sjQ9zx2pK5GpCZA+aTZweBmyR8bpGCnujdgcNCapM4l2Xdh5fonsFhqJrDWfA4j2NSiOQ2yKw8fH+8CgYEAy9GXwzQsg7TJpbRjYmzSYXSyF5jxQNQixaU72GCFz6Rq3P51TXoyZPXbZYe35qL4tia6mP0z7TktUJhMtXTtrZpi/ruWX2GrKVHae7JgKZO0YmIrcTrNf6VjqjWZ7XInuQn7aqhqUVo483nBf7tCKxgOar2b+j8qkFWBj1Lob5sCgYEAyo3+35PxeXInaQShzHbjrBBGErYPJXc/3RQVRHZuZp+6eyQNjrXue+TAEMqLnCUKwcxUinOeSj/c/RKRsejlvRFndwJy/EUQYmROr4VacooCtWQebk7oeUl5JsMVbn6UMRwnf95u97qWkxr/cNJstd91PSQSEUTW5wZmjyZ+vfcCgYAPLggAiNU75e4SwlGYlgRBL+DHsyNcsa/5Smofnmi440T2Oplf8NbqAAmcETSYH9EoN9Az7r+8TkLaus0TwqalBFeVI0F6zxphVHBSQCG7Vv3bSfZ0U23UsOuwVJenQJZGyMCJ3As3DeIp+Ap6SecOsJb6Si7gv22bHFMu6nzXOwKBgQDGLGK10LYbhu5bbmotJLns2zpnqtjj7rQ5daBXR1jF0vUlZycm6AzI/LPTRk3Xw0OQoPIKJpVCDYrhzrRxFnTjO1FoNR/5n/KN3QFqCiq2Y1lNd+9iK9ZQ7g7wyP1/1e/E6zh3H17n1MstsF84IEsE7k194SVuJlWeIXnqfIdnBw==
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
        return openssl_sign(
            $data,
            $sign,
            self::getPrivateKey(),
            OPENSSL_ALGO_SHA256
        ) ? base64_encode($sign) : null;
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