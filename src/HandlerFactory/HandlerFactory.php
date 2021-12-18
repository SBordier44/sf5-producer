<?php

declare(strict_types=1);

namespace App\HandlerFactory;

use Psr\Container\ContainerInterface;

class HandlerFactory implements HandlerFactoryInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function createHandler(string $handler): HandlerInterface
    {
        return $this->container->get($handler);
    }
}
