<?php

namespace Qufo\JWT\Provider;

use Illuminate\Support\ServiceProvider;

class LumenServiceProvider extends ServiceProvider {

    public function boot(){
        $config_file = realpath(__DIR__ . '/../../config/jwt.php');
        $this->mergeConfigFrom($config_file,'jwt');
    }
}