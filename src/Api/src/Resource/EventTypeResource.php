<?php

declare(strict_types=1);

namespace eCamp\Api\Resource;

use eCamp\Core\Entity\EventType;

class EventTypeResource {

    /** @var EventType */
    private $eventType;

    public function __construct($eventType) {
        $this->eventType = $eventType;
    }

    public function getId() {
        return $this->eventType->getId();
    }

    public function getName() {
        return $this->eventType->getName();
    }

    public function getDefaultColor() {
        return $this->eventType->getDefaultColor();
    }

    public function getDefaultNumberingStyle() {
        return $this->eventType->getDefaultNumberingStyle();
    }

}
