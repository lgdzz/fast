<?php

namespace lgdz;

/**
 * @property \lgdz\module\Util $util
 * @property \lgdz\module\Charge $charge
 * @property \lgdz\module\Sign $sign
 * @method \lgdz\module\Jwt jwt($publicKey, $privateKey)
 */
class Factory
{
    public function __get($name)
    {
        return Container::instance('\lgdz\module\\' . ucfirst($name));
    }

    public function __call($name, $arguments)
    {
        return Container::instance('\lgdz\module\\' . ucfirst($name), $arguments);
    }
}