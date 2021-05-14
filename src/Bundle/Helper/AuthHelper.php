<?php

namespace Benblub\Ftg\Bundle\Helper;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

class AuthHelper extends ApiTestCase implements AuthHelperInterface
{
    protected Client $client;
    protected array $identifier; // can be id, email or whatever is used as identifier

    public function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function setIdentifier(array $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Set here whatever your config is from lexik_jwt_authentication.yaml <user_identity_field>
     * user_identity_field: email|username|id (your Provider must support it eg loadUserBy..)
     *
     * After Create a User in a test call this Method and make requests with this User authenticated
     */
    public function setAuthenticationHeader()
    {
        $arrayKey = array_key_first($this->identifier);
        $token = $this->getUserToken($this->client, $this->identifier[$arrayKey]);
        $this->client->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    /**
     * Generate our Bearer Token
     */
    public function getUserToken(Client $client, string $identifier): string
    {
        $data = $this->identifier;

        return $client
            ->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->encode($data);
    }
}
