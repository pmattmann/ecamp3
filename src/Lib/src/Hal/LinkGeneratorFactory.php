<?php

declare(strict_types=1);

namespace eCamp\Lib\Hal;

use Mezzio\Hal\LinkGenerator\UrlGeneratorInterface;
use Psr\Container\ContainerInterface;

class LinkGeneratorFactory {
    public function __invoke(ContainerInterface $container): LinkGenerator {
        return new LinkGenerator(
            $container->get(UrlGeneratorInterface::class),
            $container->get(LinkGenerator\TemplatedUrlGenerator::class)
        );
    }
}

