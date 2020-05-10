<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceHandler;

use Doctrine\ORM\EntityManager;
use eCamp\Api\ResourceFactory\CampTypeResourceFactory;
use eCamp\Api\ResourceFactory\EventTypeResourceFactory;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Handler\AbstractHandler;
use Mezzio\Hal\Renderer\JsonRenderer;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CampTypeHandler extends AbstractHandler {

    /** @var CampTypeResourceFactory */
    private $campTypeResourceFactory;

    /** @var EventTypeResourceFactory */
    private $eventTypeResourceFactory;

    public function __construct(
        EntityManager $entityManager,
        ResourceGenerator $resourceGenerator,
        ProblemDetailsResponseFactory $problemDetailsResponseFactory,
        JsonRenderer $jsonRenderer,
        CampTypeResourceFactory $campTypeResourceFactory,
        EventTypeResourceFactory $eventTypeResourceFactory
    ) {
        parent::__construct($entityManager, $resourceGenerator, $problemDetailsResponseFactory, $jsonRenderer);
        $this->campTypeResourceFactory = $campTypeResourceFactory;
        $this->eventTypeResourceFactory = $eventTypeResourceFactory;
    }

    public function getMethod(ServerRequestInterface $request): ResponseInterface {
        $id = $request->getAttribute('id');
        if (isset($id)) {
            /** @var CampType $campType */
            $campType = $this->getRepository(CampType::class)->find($id);
            if ($campType != null) {
                if ($request->getAttribute('collection') == 'eventtypes') {
                    return $this->ok($this->eventTypeResourceFactory->createSubCollection($campType, $request));
                } else {
                    return $this->ok($this->campTypeResourceFactory->createResource($campType, $request));
                }
            }
        } else {
            $campTypes = $this->getRepository(CampType::class)->findAll();
            return $this->ok($this->campTypeResourceFactory->createCollection($campTypes, $request));
        }
        return $this->notFound();
    }

    public function postMethod(ServerRequestInterface $request): ResponseInterface {
        $organizatin = new Organization();
        $organizatin->setName('asdf');

        $campType = new CampType();
        $campType->setName('asdf');
        $campType->setIsJS(false);
        $campType->setIsCourse(false);
        $campType->setOrganization($organizatin);

        $this->getEntityManager()->persist($organizatin);
        $this->getEntityManager()->persist($campType);
        return $this->ok($this->campTypeResourceFactory->createResource($campType, $request));
    }

    public function patchMethod(ServerRequestInterface $request): ResponseInterface {
        $id = $request->getAttribute('id');
        if (isset($id)) {
            $body = $request->getParsedBody();

            $campType = $this->getRepository(CampType::class)->find($id);
            if (isset($body['name'])) { $campType->setName($body['name']); }

            return $this->getMethod($request);

        } else {
            return $this->methodNotAllowed();
        }
    }

    public function deleteMethod(ServerRequestInterface $request): ResponseInterface {
        return $this->removeEntity($request, CampType::class);
    }
}
