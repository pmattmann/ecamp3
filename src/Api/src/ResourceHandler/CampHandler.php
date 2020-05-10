<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceHandler;

use Doctrine\ORM\EntityManager;
use eCamp\Api\Resource\CampResource;
use eCamp\Api\ResourceFactory\CampResourceFactory;
use eCamp\Core\Handler\AbstractHandler;
use Mezzio\Hal\Renderer\JsonRenderer;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CampHandler extends AbstractHandler {

    /** @var CampResourceFactory */
    private $campResourceFactory;

    public function __construct(
        EntityManager $entityManager,
        ResourceGenerator $resourceGenerator,
        ProblemDetailsResponseFactory $problemDetailsResponseFactory,
        JsonRenderer $jsonRenderer,
        CampResourceFactory $campResourceFactory
    ) {
        parent::__construct($entityManager, $resourceGenerator, $problemDetailsResponseFactory, $jsonRenderer);
        $this->campResourceFactory = $campResourceFactory;
    }

    public function getMethod(ServerRequestInterface $request): ResponseInterface {
        $id = $request->getAttribute('id');

        if (isset($id)) {
            $camp = new CampResource($id);
            $resource = $this->campResourceFactory->createResource($camp, $request);
        } else {
            $camp1 = new CampResource(1);
            $camp2 = new CampResource(2);
            $resource = $this->campResourceFactory->createCollection([$camp1, $camp2], $request);
        }

        return $this->ok($this->getJsonRenderer()->render($resource));
    }
}
