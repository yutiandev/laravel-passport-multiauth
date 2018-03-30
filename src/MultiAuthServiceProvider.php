<?php

namespace Yutianx\LPM;

use Illuminate\Auth\RequestGuard;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\ResourceServer;
use Yutian\LPM\Bridge\MultiAuthUserRepository;

class MultiAuthServiceProvider extends PassportServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function register()
    {
        parent::register();
        $this->app->singleton('passport.multiauth', function () {
            return new PassportMultiAuth();
        });
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    protected function makePasswordGrant()
    {
        $grant = new PasswordGrant(
            $this->app->make(MultiAuthUserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
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