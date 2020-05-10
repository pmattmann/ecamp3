<?php

declare(strict_types=1);

namespace eCamp\Core\ResourceFactory;

use Mezzio\Hal\LinkGenerator;
use Mezzio\Hal\ResourceGenerator;

abstract class AbstractResourceFactory {

    /** @var ResourceGenerator */
    private $resourceGenerator;

    public function __construct(
        ResourceGenerator $resourceGenerator
    ) {
        $this->resourceGenerator = $resourceGenerator;
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

}
