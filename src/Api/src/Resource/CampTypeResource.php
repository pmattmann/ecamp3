<?php

declare(strict_types=1);

namespace eCamp\Api\Resource;

use eCamp\Core\Entity\CampType;

class CampTypeResource {

    /** @var CampType */
    private $campType;

    public function __construct($campType) {
        $this->campType = $campType;
    }

    public function getId() {
        return $this->campType->getId();
    }

    public function getName() {
        return $this->campType->getName();
    }

    public function getIsJS() {
        return $this->campType->getIsJS();
    }

    public function getIsCourse() {
        return $this->campType->getIsCourse();
    }
}
