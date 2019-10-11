<?php

namespace lgdz\module;

use \Exception;

class Jwt
{

    protected $privateKey;
    protected $publicKey;

    public function __construct(string $publicKey, string $privateKey)
    {
        $this->publicKey  = $publicKey;
        $this->privateKey = $privateKey;
    }


    public function issue(array $data, int $expire, string $encodeKey = ''): string
    {
        $header  = json_encode(['typ' => 'JWT', 'alg' => 'SHA256']);
        $payload = json_encode(array_merge(['exp' => time() + $expire], $data));

        if ($encodeKey) {
            $payload = Helper::util()->encrypt($payload, $encodeKey);
        }

        $jwt   = [];
        $jwt[] = Helper::util()->buildUrlSafeBase64encode($header);
        $jwt[] = Helper::util()->buildUrlSafeBase64encode($payload);
        $jwt[] = Helper::util()->buildUrlSafeBase64encode((new Sign)->setPrivateKey($this->privateKey)->make(implode('.', $jwt)));
        return implode('.', $jwt);
    }

    public function check(string $token, string $decodeKey = ''): array
    {
        $jwt = explode('.', $token);
        if (count($jwt) !== 3) {
            throw new Exception('token format error');
        }

        list($headerB64, $payloadB64, $signatrueB64) = $jwt;
        $headerString  = Helper::util()->buildUrlSafeBase64decode($headerB64);
        $payloadString = Helper::util()->buildUrlSafeBase64decode($payloadB64);
        $signatrue     = Helper::util()->buildUrlSafeBase64decode($signatrueB64);

        //check signature
        $checkSignResult = (new Sign)->setPublicKey($this->publicKey)->check("{$headerB64}.{$payloadB64}", $signatrue);
        if (!$checkSignResult) {
            throw new Exception('signature error');
        }

        if ($decodeKey) {
            $payloadString = Helper::util()->decrypt($payloadString, $decodeKey);
        }
        $payload = json_decode($payloadString, true);

        //check expire
        if ($payload['exp'] < time()) {
            throw new Exception('token invalid');
        }

        unset($payload['exp']);
        return $payload;
    }
}