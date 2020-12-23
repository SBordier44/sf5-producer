<?php

declare(strict_types=1);

namespace App\HandlerFactory;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HandlerInterface
 * @package App\HandlerFactory
 */
interface HandlerInterface
{
    /**
     * @param Request $request
     * @param null|mixed $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, $data = null, array $options = []): bool;
}
