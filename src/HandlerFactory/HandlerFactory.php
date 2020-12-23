<?php

declare(strict_types=1);

namespace App\HandlerFactory;

use Psr\Container\ContainerInterface;

/**
 * Class HandlerFactory
 * @package App\HandlerFactory
 */
class HandlerFactory implements HandlerFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createHandler(string $handler): HandlerInterface
    {
        return $this->container->get($handler);
    }
}
