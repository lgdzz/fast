<?php

namespace lgdz\module;

class Sign
{
    private $privateKey;
    private $publicKey;

    public function setPrivateKey($key)
    {
        $this->privateKey = $key;
        return $this;
    }

    public function setPublicKey($key)
    {
        $this->publicKey = $key;
        return $this;
    }

    /**
     * @param string|array $data
     * @return string
     * @throws \Exception
     */
    public function make($data): string
    {
        if (is_array($data)) {
            $data = array_filter($data);
            ksort($data);
            $dataString = urldecode(http_build_query($data));
        } else {
            $dataString = $data;
        }

        $privateKeyId = Helper::util()->getRsaPrivateKeyId($this->privateKey);

        $sign = '';
        openssl_sign($dataString, $sign, $privateKeyId, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKeyId);
        return base64_encode($sign);
    }

    public function check($data, string $sign): bool
    {
        if (is_array($data)) {
            $data = array_filter($data);
            ksort($data);
            $dataString = urldecode(http_build_query($data));
        } else {
            $dataString = $data;
        }

        $publicKeyId = Helper::util()->getRsaPublicKeyId($this->publicKey);

        $result = openssl_verify($dataString, base64_decode($sign), $publicKeyId, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKeyId);
        return $result;
    }
}