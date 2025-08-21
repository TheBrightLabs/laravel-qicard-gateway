<?php

namespace Thebrightlabs\IraqPayments;
use Illuminate\Support\Facades\Http;

class QiCardGateway
{
 // Bismillah.

    protected $config;

    public function __construct()
    {
        $this->config = config('qi_card');
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

    public function getTerminalId()
    {
        $mode = $this->config['mode'];
        return $this->config[$mode]['terminal_id'];
    }

    public function getApiHost()
    {
        $mode = $this->config['mode'];
        return $this->config[$mode]['api_host'];
    }

    public function checkStatus()
    {
        // very basic status check
        $username = $this->getUsername();
        $password = $this->getPassword();
        $apiHost = $this->getApiHost();
        return Http::withBasicAuth($username,$password)
            ->get($apiHost."/status");

    }

}
