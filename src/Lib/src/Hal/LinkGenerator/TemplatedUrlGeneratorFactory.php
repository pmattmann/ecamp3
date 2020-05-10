<?php

declare(strict_types=1);

namespace eCamp\Lib\Hal\LinkGenerator;

use eCamp\Lib\Helper\TemplatedUrlHelper;
use Mezzio\Helper\ServerUrlHelper;
use Psr\Container\ContainerInterface;

class TemplatedUrlGeneratorFactory {
    public function __invoke(ContainerInterface $container): TemplatedUrlGenerator {
        $templatedUrlHelper = $container->get(TemplatedUrlHelper::class);
        $serverUrlHelper = $container->get(ServerUrlHelper::class);
        return new TemplatedUrlGenerator(
            $templatedUrlHelper,
            $serverUrlHelper
        );
    }
}
