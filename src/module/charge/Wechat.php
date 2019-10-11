<?php

namespace lgdz\module\charge;

/**
 * @method string payByApp(array $params)
 * @method string payByWap(array $params)
 * @method string payByPc(array $params)
 * @method string payByScan(array $params)
 * @method string refund(array $params)
 * @method string query(array $params)
 */
class Wechat
{

    protected $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __call($name, $arguments)
    {
        $action = '\lgdz\module\charge\wechat\\' . ucfirst($name);
        return call_user_func_array([(new $action($this->config)), 'run'], $arguments);
    }
}