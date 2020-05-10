<?php

declare(strict_types=1);

namespace eCamp\Lib;

use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class ConfigProvider {

    public function __invoke(): array {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine' => $this->getDoctrineEntities(),
        ];
    }

    public function getDependencies() {
        return [
            'aliases' => [
                \Mezzio\Hal\LinkGenerator::class => Hal\LinkGenerator::class
            ],
            'invokables' => [
            ],
            'factories' => [
                Hal\LinkGenerator::class => Hal\LinkGeneratorFactory::class,
                Hal\LinkGenerator\TemplatedUrlGenerator::class => Hal\LinkGenerator\TemplatedUrlGeneratorFactory::class,
                Helper\TemplatedUrlHelper::class => Helper\TemplatedUrlHelperFactory::class,
                Router\TemplatedFastRouteRouter::class => Router\TemplatedFastRouteRouterFactory::class,
            ]
        ];
    }

    public function getDoctrineEntities(): array {
        return [
            'driver' => [
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                    'drivers' => [
                        'eCamp\Lib\Entity' => 'eCampLibEntity',
                    ],
                ],
                'eCampLibEntity' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }
}
