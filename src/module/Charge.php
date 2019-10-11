<?php

namespace lgdz\module;

/**
 * @method \lgdz\module\charge\Alipay alipay(array $config)
 * @method \lgdz\module\charge\Wechat wechat(array $config)
 */
class Charge
{
    public function __call($name, $arguments)
    {
        $class = '\lgdz\module\charge\\' . ucfirst($name);
        return new $class(...$arguments);
    }
}