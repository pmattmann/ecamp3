<?php

declare(strict_types=1);

namespace eCamp\Api\ResourceFactory;

use eCamp\Api\Resource\CampResource;
use eCamp\Core\ResourceFactory\AbstractResourceFactory;
use Mezzio\Hal\HalResource;
use Psr\Http\Message\ServerRequestInterface;

class CampResourceFactory extends AbstractResourceFactory {

    public function createResource(
        CampResource $camp,
        ServerRequestInterface $request
    ): HalResource {
        return $this->getResourceGenerator()->fromObject($camp, $request);
    }

    public function createCollection(
        array $camps,
        ServerRequestInterface $request
    ): HalResource {
        return new HalResource([
            'count' => count($camps)
        ], [
            $this->getLinkGenerator()->fromRoute('self', $request, 'api.camps')
        ], [
            'items' => array_map(function ($camp) use ($request) {
                return $this->getResourceGenerator()->fromObject($camp, $request);
            }, $camps)
        ]);
    }
}
