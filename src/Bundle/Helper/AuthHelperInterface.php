<?php


namespace Benblub\Ftg\Bundle\Helper;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

interface AuthHelperInterface
{
    public function setIdentifier(array $identifier);
    public function setAuthenticationHeader();
    public function getUserToken(Client $client, string $identifier);
}
