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

    public function checkStatus(string $paymentId)
    {
        // very basic status check
        $apiHost = $this->getApiHost();
        $url =  $apiHost = $this->getApiHost() . "/payments/{$paymentId}";
        $username = $this->getUsername();
        $password = $this->getPassword();

        $response =  Http::withBasicAuth($username, $password)
            ->withHeaders([
                'X-Terminal-Id' => $this->getTerminalId(),
                'Accept'        => 'application/json',
            ])
            ->get($url);

        return $response->json();
    }
}
