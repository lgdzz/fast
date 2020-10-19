<?php

namespace lgdz;

/**
 * @property \lgdz\module\Util $util
 * @property \lgdz\module\Tree $tree
 * @property \lgdz\module\Charge $charge
 * @property \lgdz\module\Sign $sign
 * @property \lgdz\module\Http $http
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
        $class = '\lgdz\module\\' . ucfirst($name);
        return new $class(...$arguments);
    }
}