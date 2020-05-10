<?php

declare(strict_types=1);

namespace eCamp\Api\Resource;

use eCamp\Core\Entity\Organization;

class OrganizationResource {

    /** @var Organization */
    private $organization;

    public function __construct($organization) {
        $this->organization = $organization;
    }

    public function getId() {
        return $this->organization->getId();
    }

    public function getName() {
        return $this->organization->getName();
    }
}
