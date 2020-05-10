<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceHandler;

use Doctrine\ORM\EntityManager;
use eCamp\Api\ResourceFactory\EventTypeResourceFactory;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Handler\AbstractHandler;
use Mezzio\Hal\Renderer\JsonRenderer;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EventTypeHandler extends AbstractHandler {

    /** @var EventTypeResourceFactory */
    private $eventTypeResourceFactory;

    public function __construct(
        EntityManager $entityManager,
        ResourceGenerator $resourceGenerator,
        ProblemDetailsResponseFactory $problemDetailsResponseFactory,
        JsonRenderer $jsonRenderer,
        EventTypeResourceFactory $eventTypeResourceFactory
    ) {
        parent::__construct($entityManager, $resourceGenerator, $problemDetailsResponseFactory, $jsonRenderer);
        $this->eventTypeResourceFactory = $eventTypeResourceFactory;
    }

    public function getMethod(ServerRequestInterface $request): ResponseInterface {
        $id = $request->getAttribute('id');
        if (isset($id)) {
            /** @var EventType $eventType */
            $eventType = $this->getRepository(EventType::class)->find($id);
            if ($eventType != null) {
                return $this->ok($this->eventTypeResourceFactory->createResource($eventType, $request));
            }
        } else {
            $eventTypes = $this->getRepository(EventType::class)->findAll();
            return $this->ok($this->eventTypeResourceFactory->createCollection($eventTypes, $request));
        }
        return $this->notFound();
    }

    public function postMethod(ServerRequestInterface $request): ResponseInterface {
        $eventType = new EventType();
        $eventType->setName('asdf');
        $eventType->setDefaultColor('#FF0000');
        $eventType->setDefaultNumberingStyle('i');

        $this->getEntityManager()->persist($eventType);
        return $this->ok($this->eventTypeResourceFactory->createResource($eventType, $request));
    }

    public function deleteMethod(ServerRequestInterface $request): ResponseInterface {
        return $this->removeEntity($request, EventType::class);
    }
}
