<?php

namespace Thebrightlabs\IraqPayments\Traits;

trait withQiCardHelpers
{

    //Bismillah
    protected $config;

    public function __construct()
    {
        $this->config = config('qi_card');

    }

}
