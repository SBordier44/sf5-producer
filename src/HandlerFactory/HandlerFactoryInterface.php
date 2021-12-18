<?php

declare(strict_types=1);

namespace App\HandlerFactory;

interface HandlerFactoryInterface
{
    public function createHandler(string $handler): HandlerInterface;
}
