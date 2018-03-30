<?php

namespace Yutianx\LPM;

use Laravel\Passport\TokenRepository;
use Laravel\Passport\Token;
use Yutianx\LPM\Facades\PassportMultiAuth;

class MultiAuthTokenRepository extends TokenRepository
{
    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function clientId()
    {
        return PassportMultiAuth::clientId();
    }

    /**
     * Get a token by the given ID.
     *
     * @param  string  $id
     * @return \Laravel\Passport\Token
     */
    public function find($id)
    {
        if ($this->clientId()) {
            return Token::where('client_id', $this->clientId())
                ->find($id);
        } else {
            return Token::find($id);
        }
    }

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param  string  $id
     * @param  int  $userId
     * @return \Laravel\Passport\Token|null
     */
    public function findForUser($id, $userId)
    {
        $token = Token::where('id', $id)
            ->where('user_id', $userId);

        if ($this->clientId()) {
            $token = $token->where('client_id', $this->clientId());
        }

        return $token->first();
    }

    /**
     * Get the token instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($userId)
    {
        $token = Token::where('user_id', $userId);
        if ($this->clientId()) {
            $token = $token->where('client_id', $this->clientId());
        }
        return $token->get();
    }

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Laravel\Passport\Token|null
     */
    public function getValidToken($user, $client)
    {
        $token = $client->tokens()
            ->whereUserId($user->getKey())
            ->whereRevoked(0)
            ->where('expires_at', '>', Carbon::now());

        if ($this->clientId()) {
            $token = $token->where('client_id', $this->clientId());
        }

        return $token->first();
    }

    /**
     * Revoke an access token.
     *
     * @param  string  $id
     * @return mixed
     */
    public function revokeAccessToken($id)
    {
        $token = Token::where('id', $id);

        if ($this->clientId()) {
            $token = $token->where('client_id', $this->clientId());
        }

        return $token->update(['revoked' => true]);
    }

    /**
     * Find a valid token for the given user and client.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Laravel\Passport\Token|null
     */
    public function findValidToken($user, $client)
    {
        $token = $client->tokens()
            ->whereUserId($user->getKey())
            ->whereRevoked(0)
            ->where('expires_at', '>', Carbon::now());

        if ($this->clientId()) {
            $token = $token->where('client_id', $this->clientId());
        }

        return $token->latest('expires_at')
            ->first();
    }
}