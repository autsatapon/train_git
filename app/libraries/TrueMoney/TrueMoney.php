<?php

namespace TrueMoney;

use Teepluss\Api\Api;
use Illuminate\Config\Repository;

class TrueMoney{
    public $config;
    protected $api;
    public function __construct(Repository $config, Api $api){
        $this->config = $config;
        $this->api = $api;
    }
}

