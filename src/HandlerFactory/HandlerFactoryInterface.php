<?php

declare(strict_types=1);

namespace App\HandlerFactory;

/**
 * Interface HandlerFactoryInterface
 * @package App\HandlerFactory
 */
interface HandlerFactoryInterface
{
    /**
     * @param string $handler
     * @return HandlerInterface
     */
    public function createHandler(string $handler): HandlerInterface;
}
