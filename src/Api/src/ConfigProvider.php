<?php

declare(strict_types=1);

namespace eCamp\Api;

use Laminas\Hydrator\ClassMethodsHydrator;
use Mezzio\Hal\Metadata\MetadataMap;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;

class ConfigProvider {

    public function __invoke(): array {
        return [
            'dependencies' => $this->getDependencies(),
            MetadataMap::class => $this->getMetadataMap(),
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

    public function getMetadataMap() {
        return [
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Resource\OrganizationResource::class,
                'route' => 'api.organizations',
                'extractor' => ClassMethodsHydrator::class,
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Resource\CampTypeResource::class,
                'route' => 'api.camptypes',
                'extractor' => ClassMethodsHydrator::class,
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Resource\EventTypeResource::class,
                'route' => 'api.eventtypes',
                'extractor' => ClassMethodsHydrator::class,
            ],

            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Resource\CampResource::class,
                'route' => 'api.camps',
                'extractor' => ClassMethodsHydrator::class,
            ],
        ];
    }

}
