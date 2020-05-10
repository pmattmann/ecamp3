<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceFactory;

use eCamp\Api\Resource\EventTypeResource;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\EventType;
use eCamp\Core\ResourceFactory\AbstractResourceFactory;
use Mezzio\Hal\HalResource;
use Psr\Http\Message\ServerRequestInterface;

class EventTypeResourceFactory extends AbstractResourceFactory {

    public function createResource(
        EventType $eventType,
        ServerRequestInterface $request
    ): HalResource {
        $eventTypeResource = new EventTypeResource($eventType);

        $resource = $this->getResourceGenerator()->fromObject($eventTypeResource, $request);

        return $resource;
    }

    public function createCollection(
        array $eventTypes,
        ServerRequestInterface $request
    ): HalResource {
        return new HalResource([
            'count' => count($eventTypes)
        ], [
            $this->getLinkGenerator()->fromRoute('self', $request, 'api.eventtypes'),
        ], [
            'items' => array_map(function (EventType $eventType) use ($request) {
                $eventTypeResource = new EventTypeResource($eventType);
                return $this->getResourceGenerator()->fromObject($eventTypeResource, $request);
            }, $eventTypes)
        ]);
    }

    public function createSubCollection(
        CampType $campType,
        ServerRequestInterface $request
    ): HalResource {
        $eventTypes = $campType->getEventTypes()->toArray();
        return new HalResource([
            'count' => count($eventTypes)
        ], [
            $this->getLinkGenerator()->fromRoute(
                'self',
                $request,
                'api.camptypes',
                ['id' => $campType->getId(), 'collection' => 'eventtypes']
            ),
        ], [
            'items' => array_map(function (EventType $eventType) use ($request) {
                $eventTypeResource = new EventTypeResource($eventType);
                return $this->getResourceGenerator()->fromObject($eventTypeResource, $request);
            }, $eventTypes)
        ]);
    }
}
