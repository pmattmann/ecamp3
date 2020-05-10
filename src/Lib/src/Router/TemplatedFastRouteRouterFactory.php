<?php

declare(strict_types=1);

namespace eCamp\Lib\Router;

use Mezzio\Router\FastRouteRouter;
use Psr\Container\ContainerInterface;

class TemplatedFastRouteRouterFactory {
    public function __invoke(ContainerInterface $container): TemplatedFastRouteRouter {
        /** @var FastRouteRouter $fastRouteRouter */
        $fastRouteRouter = $container->get(FastRouteRouter::class);

        return new TemplatedFastRouteRouter($fastRouteRouter);
    }
}
