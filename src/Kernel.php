<?php

declare(strict_types=1);

namespace App;

use App\DependencyInjection\Compiler\HandlerPass;
use App\DependencyInjection\Compiler\MakerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HandlerPass());
        $container->addCompilerPass(new MakerPass());
    }
}
