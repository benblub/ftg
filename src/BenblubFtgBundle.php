<?php
namespace Benblub\Ftg;

class BenblubFtgBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}