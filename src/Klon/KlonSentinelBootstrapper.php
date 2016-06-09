<?php
namespace Klon;

use Cartalyst\Sentinel\Native\SentinelBootstrapper;

class KlonSentinelBootstrapper extends SentinelBootstrapper
{
    public function __construct()
    {
        if(file_exists(__DIR__ . '/../../app/config/cartalyst.sentinel.php')) {
            $this->config = require __DIR__ . '/../../app/config/cartalyst.sentinel.php';
        }
    }
}
