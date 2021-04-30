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
required: create your UserFactory and set defaults. `php bin/console make:factory User`

Create your Foundry Classes in /tests/Factory and setup Defaults. 
Add to your Factories myDefaults
```php
    public static function myDefaults(): array
    {
        $class = new self();
        
        return $class->getDefaults();
    }
```

## Use
`php bin/console make:ftg`  
`php bin/console make:ftg [<entity>]`
