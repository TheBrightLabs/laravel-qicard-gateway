<?php

namespace Thebrightlabs\QiCard;

trait withQiCardConfigs
{

    //Bismillah
    protected $config;

    public function __construct()
    {
        $this->config = config('qi_card');

    }

    public function getTerminalId()
    {
        $mode = $this->config['mode'];
        return $this->config[$mode]['terminal_id'];
    }


    public function getUsername()
    {
        $mode = $this->config['mode'];
        return $this->config[$mode]['username'];
    }

    public function getPassword()
    {
        $mode = $this->config['mode'];
        return $this->config[$mode]['password'];
    }

    public function getApiHost()
    {
        $mode = $this->config['mode'];
        return $this->config[$mode]['api_host'];
    }

    public function getFinishPaymentUrl()
    {
        return $this->config['finishPaymentUrl'];
    }


}
