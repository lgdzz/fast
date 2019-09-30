<?php

namespace lgdz;

/**
 * @property \lgdz\module\Test $test
 */
class Factory
{
    public function __get($name)
    {
        return Container::instance('\lgdz\module\\' . ucfirst($name));
    }
}