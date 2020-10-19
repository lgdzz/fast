<?php

declare (strict_types=1);

namespace lgdz\module;

use Curl\Curl;

class Http
{
    public function get(string $url, array $data = [], \Closure $before = null, \Closure $after = null)
    {
        return $this->box($before, $after, function (Curl $curl) use ($url, $data) {
            return $curl->get($url, $data);
        });
    }

    public function post(string $url, array $data = [], \Closure $before = null, \Closure $after = null)
    {
        return $this->box($before, $after, function (Curl $curl) use ($url, $data) {
            return $curl->post($url, $data);
        });
    }

    public function put(string $url, array $data = [], \Closure $before = null, \Closure $after = null)
    {
        return $this->box($before, $after, function (Curl $curl) use ($url, $data) {
            return $curl->put($url, $data);
        });
    }

    public function delete(string $url, array $data = [], \Closure $before = null, \Closure $after = null)
    {
        return $this->box($before, $after, function (Curl $curl) use ($url, $data) {
            return $curl->delete($url, [], $data);
        });
    }

    private function box(\Closure $before = null, \Closure $after = null, \Closure $request)
    {
        try {
            $curl = new Curl();
            $curl->setHeader('content-type', 'application/json;charset=UTF-8');
            $curl->setTimeout(10);
            if (!is_null($before)) {
                $before($curl);
            }
            $request($curl);
            $curl->close();
            if (!is_null($after)) {
                return $after($curl->getResponse());
            } else {
                return $curl->getResponse();
            }
        } catch (\Throwable $e) {
            error($e->getMessage());
        }
    }
}