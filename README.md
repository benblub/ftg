# ftg
A Functional Test Generator.

### Requires
- https://github.com/zenstruck/foundry

### Install
`composer require benblub/ftg "dev-main"`

### Config Api Platform / Symfony / Foundry
There is no autoconfig yet..

add to `config/bundles.php`
```
Benblub\Ftg\BenblubFtgBundle::class => ['dev' => true],
```

add to `services.yaml`
```
    Benblub\Ftg\Bundle\Maker\MakeFunctionalTest:
        tags: ['maker.command']
```
## Foundry

This Generator make use of Foundry Factories. For every Testclass we generate we need to have a Factory too. 
Create your Factory `php bin/console make:factory User --test` and set defaults. The defaults are at least all required fields from your Entity. 

Add Method myDefaults to your Factories
```php
    public static function myDefaults(): array
    {
        $class = new self();
        
        return $class->getDefaults();
    }
```

## Extends ApiTestCase / implement AuthHelperInterface

Your Test classes extend any class which extends ApiTestCase from ApiPlatform. 
To use Auth you need to implement the AuthHelperInterface like shown in the examble.
also needs config set "custom_auth: true" (config is not implemented yet)

else you can use the defaults from AuthHelper (use id as identifier)


```php
<?php

namespace App\Test;

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
```


## Use
Allow CRUD Test `php bin/console make:ftg`  
Deny CRUD Test `php bin/console make:ftg --deny=deny`  

interactive Questions  
**Question: role for the auth User eg user, admin or whatever**  
Type with which ROLE this test should be created. user for ROLE_USER, admin for ROLE_ADMIN or all other roles. 
any means not auth header will be set.

**Question Entity class to create a FunctionalTest for**  
chose the entity which you want test

## Why this Bundle
Create Functional CRUD tests is mostly same for all Entities and over different Projetcs. With use of a Generator there are various Benefits.
- Tests looks same
- no boring write of always same code
- speedup writing tests and focus on tests which test the individual App parts
- Easy way to Replace tests if new Version/improvements available
