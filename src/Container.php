<?php

namespace lgdz;

class Container
{
    protected static $instances = [];

    public static function instance(string $class, array $vars = [], bool $newInstance = false)
    {
        if (is_null(self::$instances[$class]) || $newInstance) {
            self::$instances[$class] = new $class($vars);
        }

        return self::$instances[$class];
    }
}