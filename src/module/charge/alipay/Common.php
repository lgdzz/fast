<?php

namespace lgdz\module\charge\alipay;

class Common
{
    protected $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }
}