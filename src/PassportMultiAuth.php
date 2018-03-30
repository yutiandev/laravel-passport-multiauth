<?php

namespace Yutianx\LPM;

class PassportMultiAuth
{
    /**
     * @var string
     */
    protected $guard = 'api';

    public function guard()
    {
        $this->getGuard();
        return $this->guard;
    }


    /**
     * Get guard from Header Accept
     */
    protected function getGuard()
    {
        $accepts = explode(';', response()->headers->get('Accept'));
        $guards = array_keys(config('auth.guards'));

        foreach ($accepts as $accept) {
            if (preg_match('/^application\/vnd\.passport\.(' . implode('|', $guards) . ')$/i', trim($accept), $match)) {
                $this->guard = $match[1];
                continue;
            }
        }
    }
}