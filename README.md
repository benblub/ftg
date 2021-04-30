# ftg
A Functional Test Generator.

### Require
PHP >= 7.4
zenstruck/foundry >= 1.10 < 2

### Install
`composer require benblub/ftg "dev-main"`

### Config ApiPlatform/Symfony

add to `services.yaml`
```
    Benblub\Ftg\Bundle\Maker\MakeFunctionalTest:
        tags: ['maker.command']
```
## Foundry
required: create your UserFactory and set defaults. `php bin/console make:factory User --test`

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

## Help
If Symfony autoconfig not work add to `config/bundles.php`
```
Benblub\Ftg\BenblubFtgBundle::class => ['dev' => true],
```
