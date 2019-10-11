<?php

namespace lgdz;

class Container
{
    protected static $instances = [];

    public static function getInstances()
    {
        return self::$instances;
    }

    public static function instance(string $class, array $arguments = [], bool $newInstance = false)
    {
        if (!isset(self::$instances[$class]) || $newInstance) {
            self::$instances[$class] = new $class(...$arguments);
        }

        return self::$instances[$class];
    }
}