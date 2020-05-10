<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceFactory;

use eCamp\Api\Resource\OrganizationResource;
use eCamp\Core\Entity\Organization;
use eCamp\Core\ResourceFactory\AbstractResourceFactory;
use Mezzio\Hal\HalResource;
use Psr\Http\Message\ServerRequestInterface;

class OrganizationResourceFactory extends AbstractResourceFactory {

    public function createResource(
        Organization $organization,
        ServerRequestInterface $request
    ): HalResource {
        $organizationResource = new OrganizationResource($organization);

        $resource = $this->getResourceGenerator()->fromObject($organizationResource, $request);

        return $resource;
    }

    public function createCollection(
        array $organizations,
        ServerRequestInterface $request
    ): HalResource {
        return new HalResource([
            'count' => count($organizations)
        ], [
            $this->getLinkGenerator()->fromRoute('self', $request, 'api.organizations')
        ], [
            'items' => array_map(function (Organization $organization) use ($request) {
                $organizationResource = new OrganizationResource($organization);
                return $this->getResourceGenerator()->fromObject($organizationResource, $request);
            }, $organizations)
        ]);
    }
}
