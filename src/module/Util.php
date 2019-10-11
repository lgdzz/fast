<?php

namespace lgdz\module;

use \Exception;

class Util
{
    public function getRsaPrivateKeyId($key)
    {
        if (is_resource($key)) {
            return $key;
        } else {
            $keyId = openssl_get_privatekey($this->getRsaKeyValue($key, true));
            if (empty($keyId)) {
                throw new Exception('您使用的私钥格式错误，请检查RSA私钥配置');
            }
            return $keyId;
        }
    }

    public function getRsaPublicKeyId($key)
    {
        if (is_resource($key)) {
            return $key;
        } else {
            $keyId = openssl_get_publickey($this->getRsaKeyValue($key, false));
            if (empty($keyId)) {
                throw new Exception('商户RSA公钥错误。请检查公钥文件格式是否正确');
            }
            return $keyId;
        }
    }

    public function getRsaKeyValue($key, bool $isPrivate = true)
    {
        $keyStr = str_replace(PHP_EOL, '', $key);
        if ($isPrivate) {
            $beginStr = ['-----BEGIN RSA PRIVATE KEY-----', '-----BEGIN PRIVATE KEY-----'];
            $endStr   = ['-----END RSA PRIVATE KEY-----', '-----END PRIVATE KEY-----'];
        } else {
            $beginStr = ['-----BEGIN PUBLIC KEY-----', ''];
            $endStr   = ['-----END PUBLIC KEY-----', ''];
        }
        $keyStr = str_replace($beginStr, ['', ''], $keyStr);
        $keyStr = str_replace($endStr, ['', ''], $keyStr);

        $rsaKey = $beginStr[0] . PHP_EOL . wordwrap($keyStr, 64, PHP_EOL, true) . PHP_EOL . $endStr[0];

        return $rsaKey;
    }

    public function encrypt(string $string, $privateKey): string
    {
        $keyId = Helper::util()->getRsaPrivateKeyId($privateKey);

        $encrypted = '';
        foreach (str_split($string, 117) as $chunk) {
            openssl_private_encrypt($chunk, $temp, $keyId);
            $encrypted .= $temp;
        }
        openssl_free_key($keyId);

        return Helper::util()->buildUrlSafeBase64encode($encrypted);
    }

    public function decrypt(string $string, $publicKey): string
    {
        $keyId = Helper::util()->getRsaPublicKeyId($publicKey);

        $decrypted = '';
        foreach (str_split(Helper::util()->buildUrlSafeBase64decode($string), 128) as $chunk) {
            openssl_public_decrypt($chunk, $temp, $keyId);
            $decrypted .= $temp;
        }
        openssl_free_key($keyId);
        return $decrypted;
    }

    public function buildUrlSafeBase64encode(string $string)
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
        return $data;
    }

    public function buildUrlSafeBase64decode(string $string)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function buildTradeNo(int $length = 19): string
    {
        if ($length < 15) {
            throw new Exception('交易号长度必须>=15');
        }
        $len  = $length - 14;
        $rand = '';
        for ($i = 0; $i < $len; $i++) {
            $rand .= rand(0, 9);
        }
        return date('YmdHis') . $rand;
    }

    public function buildRandString(int $length = 5): string
    {
        $array = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        shuffle($array);
        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= $array[array_rand($array, 1)];
        }
        return $value;
    }

    public function buildRandNumber(int $length = 5): string
    {
        $array = range(0, 9);
        shuffle($array);
        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= $array[array_rand($array, 1)];
        }
        return $value;
    }

    public function buildCamelize(array $data, string $separator = '_'): array
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $data = $separator . str_replace($separator, ' ', strtolower($data));
        return json_decode(ltrim(str_replace(' ', '', ucwords($data)), $separator), true);
    }

    public function buildUncamelize(array $data, string $separator = '_'): array
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        return json_decode(strtolower(preg_replace('/([a-z])([A-Z])/', '$1' . $separator . '$2', $data)), true);
    }
}