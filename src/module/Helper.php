<?php

namespace lgdz\module;

use lgdz\Container;

class Helper
{
    public static function util(): Util
    {
        return Container::instance('\lgdz\module\Util');
    }
}