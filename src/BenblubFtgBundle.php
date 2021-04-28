<?php

namespace Benblub\Ftg;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BenblubFtgBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}