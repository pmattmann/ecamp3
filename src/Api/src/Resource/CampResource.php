<?php

declare(strict_types=1);

namespace eCamp\Api\Resource;

class CampResource {

    private $camp;

    public function __construct($camp) {
        $this->camp = $camp;
    }

    public function getId() {
        return $this->camp;
    }

    public function getTitle() {
        return 'title';
    }

    public function getMotto() {
        return 'motto';
    }
}
