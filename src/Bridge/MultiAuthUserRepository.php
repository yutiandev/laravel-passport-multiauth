<?php

namespace Yutian\LPM\Bridge;

use Laravel\Passport\Bridge\UserRepository;
use RuntimeException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Laravel\Passport\Bridge\User;
use Yutianx\LPM\Facades\PassportMultiAuth;

class MultiAuthUserRepository extends UserRepository
{
    /**
     * @param string $username
     * @param string $password
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @return void|User
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $guard = PassportMultiAuth::guard();
        $provider = config('auth.guards.'.$guard.'.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        if (method_exists($model, 'findForPassport')) {
            $user = (new $model)->findForPassport($username);
        } else {
            $user = (new $model)->where('email', $username)->first();
        }

        if (! $user) {
            return;
        } elseif (method_exists($user, 'validateForPassportPasswordGrant')) {
            if (! $user->validateForPassportPasswordGrant($password)) {
                return;
            }
        } elseif (! $this->hasher->check($password, $user->getAuthPassword())) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }
}