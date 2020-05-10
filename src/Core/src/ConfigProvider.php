<?php

declare(strict_types=1);

namespace eCamp\Core;

use Doctrine\ORM\EntityManager;
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
            'invokables' => [
            ],
            'factories' => [
            ]
        ];
    }

    public function getDoctrineEntities(): array {
        return [
            'driver' => [
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                    'drivers' => [
                        'eCamp\Core\Entity' => 'eCampCoreEntity',
                    ],
                ],
                'eCampCoreEntity' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }
}
