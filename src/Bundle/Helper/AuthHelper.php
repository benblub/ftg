<?php

namespace Benblub\Ftg\Bundle\Helper;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

class AuthHelper extends ApiTestCase implements AuthHelperInterface
{
    protected Client $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * Set here whatever your config is from lexik_jwt_authentication.yaml <user_identity_field>
     * user_identity_field: email|username|id (your Provider must support it eg loadUserBy..)
     *
     * After Create a User in a test call this Method and make requests with this User authenticated
     */
    public function setAuthenticationHeader(string $id)
    {
        $token = $this->getUserToken($this->client, $id);
        $this->client->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    /**
     * Generate our Bearer Token
     */
    public function getUserToken(Client $client, string $id): string
    {
        $data = ['id' => $id];

        return $client
            ->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->encode($data);
    }
}
