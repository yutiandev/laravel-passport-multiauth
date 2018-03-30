<?php

namespace Yutianx\LPM;

class PassportMultiAuth
{
    /**
     * @var string
     */
    protected $provider = '';

    /**
     * Get current provider name
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function provider()
    {
        $this->getProvider();
        return empty($this->provider) ?
            config('auth.guards.api.provider') :
            $this->provider;
    }

    /**
     * Get current client ID
     * @return \Illuminate\Config\Repository|mixed
     */
    public function clientId()
    {
        $provider = PassportMultiAuth::provider();
        return config('auth.providers.' . $provider . '.client_id');
    }

    /**
     * Get provider from Header Accept
     */
    protected function getProvider()
    {
        $accepts = explode(';', request()->headers->get('Accept'));
        $providers = array_keys(config('auth.providers'));

        foreach ($accepts as $accept) {
            if (preg_match('/^application\/vnd\.passport\.(' . implode('|', $providers) . ')$/i', trim($accept), $match)) {
                $this->provider = $match[1];
                continue;
            }
        }
    }
}