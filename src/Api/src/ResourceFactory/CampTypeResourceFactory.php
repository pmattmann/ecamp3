<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceFactory;

use eCamp\Api\Resource\CampTypeResource;
use eCamp\Api\Resource\EventTypeResource;
use eCamp\Api\Resource\OrganizationResource;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\EventType;
use eCamp\Core\ResourceFactory\AbstractResourceFactory;
use Mezzio\Hal\HalResource;
use Psr\Http\Message\ServerRequestInterface;

class CampTypeResourceFactory extends AbstractResourceFactory {

    public function createResource(
        CampType $campType,
        ServerRequestInterface $request
    ): HalResource {
        $resource = $this->createBaseResource($campType, $request);

        $organizationResource = new OrganizationResource($campType->getOrganization());
        $resource = $resource->withElement(
            'organization',
            $this->getResourceGenerator()->fromObject($organizationResource, $request)
        );
        $resource = $resource->withElement(
            'eventtypes',
            array_map(
                function(EventType $eventType) use ($request) {
                    $eventTypeResource = new EventTypeResource($eventType);
                    return $this->getResourceGenerator()->fromObject($eventTypeResource, $request);
                },
                $campType->getEventTypes()->toArray()
            )
        );

        return $resource;
    }

    public function createCollection(
        array $campTypes,
        ServerRequestInterface $request
    ): HalResource {
        return new HalResource([
            'count' => count($campTypes)
        ], [
            $this->getLinkGenerator()->fromRoute('self', $request, 'api.camptypes'),
        ], [
            'items' => array_map(function (CampType $campType) use ($request) {
                return $this->createBaseResource($campType, $request);
            }, $campTypes)
        ]);
    }


    private function createBaseResource(
        CampType $campType,
        ServerRequestInterface $request
    ): HalResource {
        $campTypeResource = new CampTypeResource($campType);

        $resource = $this->getResourceGenerator()->fromObject($campTypeResource, $request);
        $resource = $resource->withLink($this->getLinkGenerator()->fromRoute(
            'organization',
            $request,
            'api.organizations',
            ['id' => $campType->getOrganization()->getId()]
        ));
        $resource = $resource->withLink($this->getLinkGenerator()->fromRoute(
            'eventtypes',
            $request,
            'api.camptypes',
            ['id' => $campType->getId(), 'collection' => 'eventtypes']
        ));


        return $resource;
    }
}
