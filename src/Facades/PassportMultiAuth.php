<?php

namespace Yutianx\LPM\Facades;

use Illuminate\Support\Facades\Facade;

class PassportMultiAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'passport.multiauth';
    }
}