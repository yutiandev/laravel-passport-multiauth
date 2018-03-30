<?php

namespace Yutianx\LPM;

use Illuminate\Auth\RequestGuard;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Support\Facades\Auth;
use Yutianx\LPM\Facades\PassportMultiAuth as PassportMultiAuthFacade;

class MultiAuthServiceProvider extends PassportServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function register()
    {
        $this->registerMultiAuth();

        $this->setProvider();

        parent::register();
    }

    /**
     * Register multi auth.
     */
    protected function registerMultiAuth()
    {
        $this->app->singleton('passport.multiauth', function () {
            return new PassportMultiAuth;
        });
    }

    /**
     * Reset provider
     */
    protected function setProvider()
    {
        $provider = PassportMultiAuthFacade::provider();

        config([
            'auth.guards.api.provider' => $provider,
        ]);
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return \Illuminate\Auth\RequestGuard
     */
    protected function makeGuard(array $config)
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new TokenGuard(
                $this->app->make(ResourceServer::class),
                Auth::createUserProvider($config['provider']),
                $this->app->make(MultiAuthTokenRepository::class),
                $this->app->make(ClientRepository::class),
                $this->app->make('encrypter')
            ))->user($request);
        }, $this->app['request']);
    }
}