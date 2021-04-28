# ftg
A Functional Test Generator.

### Install
`composer require benblub/ftg "dev-main"`

### Config Api Platform / Symfony

add to `services.yaml`
```
    Benblub\Ftg\Bundle\Maker\MakeFunctionalTest:
        tags: ['maker.command']
```

## Use
`php bin/console make:ftg`  
`php bin/console make:ftg [<entity>]`