<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventCategoryHydrator;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use ZF\ApiProblem\ApiProblem;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\ServiceManager\EntityFilterManager;
use Zend\Hydrator\HydratorPluginManager;


class EventCategoryService extends AbstractEntityService {
    public function __construct
    (   ServiceUtils $serviceUtils
    ) {
        parent::__construct(
            $serviceUtils,
            EventCategory::class,
            EventCategoryHydrator::class
        );
    }

    /**
     * @param mixed $data
     * @return EventCategory|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        if (!isset($data->color)) {
            $data->color = null;
        }
        if (!isset($data->numbering_style)) {
            $data->numbering_style = null;
        }

        /** @var EventCategory $eventCategory */
        $eventCategory = parent::create($data);

        if ($eventCategory instanceof ApiProblem) {
            return $eventCategory;
        }

        /** @var EventType $eventType */
        $eventType = $this->getEntityFromData(EventType::class, $data, 'event_type');
        $eventCategory->setEventType($eventType);

        /** @var Camp $camp */
        $camp = $this->getEntityFromData(Camp::class, $data, 'camp');
        $camp->addEventCategory($eventCategory);

        return $eventCategory;
    }
}
