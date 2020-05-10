<?php

declare(strict_types=1);

namespace eCamp\Core\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Laminas\Diactoros\Response;
use Mezzio\Hal\HalResource;
use Mezzio\Hal\LinkGenerator;
use Mezzio\Hal\Renderer\JsonRenderer;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

abstract class AbstractHandler implements RequestHandlerInterface {

    /** @var EntityManager */
    private $entityManager;

    /** @var ResourceGenerator */
    private $resourceGenerator;

    /** @var ProblemDetailsResponseFactory */
    private $problemDetailsResponseFactory;

    /** @var JsonRenderer */
    private $jsonRenderer;

    public function __construct(
        EntityManager $entityManager,
        ResourceGenerator $resourceGenerator,
        ProblemDetailsResponseFactory $problemDetailsResponseFactory,
        JsonRenderer $jsonRenderer
    ) {
        $this->entityManager = $entityManager;
        $this->resourceGenerator = $resourceGenerator;
        $this->jsonRenderer = $jsonRenderer;
        $this->problemDetailsResponseFactory = $problemDetailsResponseFactory;
    }


    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager {
        return $this->entityManager;
    }

    /**
     * @param $entityName
     * @return EntityRepository
     */
    public function getRepository($entityName) {
        return $this->entityManager->getRepository($entityName);
    }

    /**
     * @return ResourceGenerator
     */
    public function getResourceGenerator(): ResourceGenerator {
        return $this->resourceGenerator;
    }

    /**
     * @return LinkGenerator
     */
    public function getLinkGenerator(): LinkGenerator {
        return $this->resourceGenerator->getLinkGenerator();
    }

    /**
     * @return JsonRenderer
     */
    public function getJsonRenderer(): JsonRenderer {
        return $this->jsonRenderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Throwable
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $method = strtolower($request->getMethod()) . 'Method';

        return $this->getEntityManager()->transactional(function() use ($request, $method) {
            return call_user_func([$this, $method], $request);
        });
    }

    public function postMethod(ServerRequestInterface $request): ResponseInterface {
        return $this->methodNotImplemented($request, 'POST');
    }

    public function getMethod(ServerRequestInterface $request): ResponseInterface {
        return $this->methodNotImplemented($request, 'GET');
    }

    public function patchMethod(ServerRequestInterface $request): ResponseInterface {
        return $this->methodNotImplemented($request, 'PATCH');
    }

    public function deleteMethod(ServerRequestInterface $request): ResponseInterface {
        return $this->methodNotImplemented($request, 'DELETE');
    }


    /**
     * @param ServerRequestInterface $request
     * @param $entityName
     * @param string $idAttribute
     * @return ResponseInterface
     * @throws ORMException
     */
    public function removeEntity(ServerRequestInterface $request, $entityName, $idAttribute = 'id'): ResponseInterface {
        $id = $request->getAttribute($idAttribute);

        if ($id != null) {
            $entity = $this->getRepository($entityName)->find($id);

            if ($entity != null) {
                $this->entityManager->remove($entity);
            }
            return $this->noContent();
        }

        return $this->methodNotAllowed();
    }

    /**
     * @param string|HalResource $data
     * @param $headers
     * @return ResponseInterface
     */
    public function ok($data, array $headers = []): ResponseInterface {
        if ($data instanceof HalResource) {
            $data = $this->getJsonRenderer()->render($data);
        }
        return new Response\TextResponse($data, 200, $headers);
    }

    /**
     * @param array $headers
     * @return ResponseInterface
     */
    public function noContent(array $headers = []): ResponseInterface {
        return new Response\EmptyResponse(204, $headers);
    }

    public function notFound(array $headers = []): ResponseInterface {
        return new Response\EmptyResponse(404, $headers);
    }

    /**
     * @param array $headers
     * @return ResponseInterface
     */
    public function methodNotAllowed(array $headers = []): ResponseInterface {
        return new Response\EmptyResponse(405, $headers);
    }

    /**
     * @param ServerRequestInterface $request
     * @param $method
     * @return ResponseInterface
     */
    public function methodNotImplemented(ServerRequestInterface $request, $method): ResponseInterface {
        return $this->problemDetailsResponseFactory->createResponse(
            $request,
            501,
            "Method '{$method}' is not implemented"
        );
    }
}
