<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class SsoProvider extends AbstractProvider
{
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            config('services.sso.base_url') . '/oauth/authorize',
            $state
        );
    }

    protected function getTokenUrl(): string
    {
        return config('services.sso.base_url') . '/oauth/token';
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get(
            config('services.sso.base_url') . '/oauth/userinfo',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id'    => $user['id']    ?? $user['sub']   ?? null,
            'name'  => $user['name']  ?? $user['nama']  ?? null,
            'email' => $user['email'] ?? null,
        ]);
    }
}
