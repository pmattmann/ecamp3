<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceHandler;

use Doctrine\ORM\EntityManager;
use eCamp\Api\ResourceFactory\OrganizationResourceFactory;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Handler\AbstractHandler;
use Mezzio\Hal\Renderer\JsonRenderer;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrganizationHandler extends AbstractHandler {

    /** @var OrganizationResourceFactory */
    private $organizationResourceFactory;

    public function __construct(
        EntityManager $entityManager,
        ResourceGenerator $resourceGenerator,
        ProblemDetailsResponseFactory $problemDetailsResponseFactory,
        JsonRenderer $jsonRenderer,
        OrganizationResourceFactory $organizationResourceFactory
    ) {
        parent::__construct($entityManager, $resourceGenerator, $problemDetailsResponseFactory, $jsonRenderer);
        $this->organizationResourceFactory = $organizationResourceFactory;
    }

    public function getMethod(ServerRequestInterface $request): ResponseInterface {
        $id = $request->getAttribute('id');
        if (isset($id)) {
            /** @var Organization $organization */
            $organization = $this->getRepository(Organization::class)->find($id);
            if ($organization != null) {
                return $this->ok($this->organizationResourceFactory->createResource($organization, $request));
            }
        } else {
            $organizations = $this->getRepository(Organization::class)->findAll();
            return $this->ok($this->organizationResourceFactory->createCollection($organizations, $request));
        }
        return $this->notFound();
    }

}
