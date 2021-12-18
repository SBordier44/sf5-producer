<?php

declare(strict_types=1);

namespace App\HandlerFactory;

use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    public function handle(Request $request, mixed $data = null, array $options = []): bool;
}
