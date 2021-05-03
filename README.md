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

add this Method to your Factories
Add to your Factories myDefaults
```php
    public static function myDefaults(): array
    {
        $class = new self();
        
        return $class->getDefaults();
    }
```

## Extends ApiTestCase

In our Cases the Api requests need some Authentication. A a helper Class for Auth like shown here. 
The generate Tests set then auth. Currently there is no Option for disable auth on generated test xy..

```php
<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

class CustomApiTestCase extends ApiTestCase
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
    protected function setAuthenticationHeader(string $id)
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
    protected function getUserToken(Client $client, string $id): string
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
`php bin/console make:ftg`  
`php bin/console make:ftg [<entity>]`
