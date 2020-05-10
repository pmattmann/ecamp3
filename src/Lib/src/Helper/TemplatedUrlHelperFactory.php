<?php

declare(strict_types=1);

namespace eCamp\Lib\Helper;

use eCamp\Lib\Router\TemplatedFastRouteRouter;
use Psr\Container\ContainerInterface;

class TemplatedUrlHelperFactory {
    public function __invoke(ContainerInterface $container): TemplatedUrlHelper {
        return new TemplatedUrlHelper(
            $container->get(TemplatedFastRouteRouter::class)
        );
    }
}
