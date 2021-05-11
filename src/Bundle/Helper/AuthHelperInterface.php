<?php


namespace Benblub\Ftg\Bundle\Helper;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

interface AuthHelperInterface
{
    public function setUp();
    public function setAuthenticationHeader(string $id);
    public function getUserToken(Client $client, string $id);
}
