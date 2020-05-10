<?php

declare(strict_types=1);

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="event_types")
 */
class EventType extends BaseEntity {
    public function __construct() {
        parent::__construct();

        $this->campTypes = new ArrayCollection();
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $defaultColor = '#1fa2df';

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $defaultNumberingStyle;

    /**
     * @ORM\ManyToMany(targetEntity="CampType", mappedBy="eventTypes")
     */
    protected $campTypes;


    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getDefaultColor() {
        return $this->defaultColor;
    }

    public function setDefaultColor($defaultColor) {
        $this->defaultColor = $defaultColor;
    }


    /**
     * @return string
     */
    public function getDefaultNumberingStyle() {
        return $this->defaultNumberingStyle;
    }

    public function setDefaultNumberingStyle($defaultNumberingStyle) {
        $this->defaultNumberingStyle = $defaultNumberingStyle;
    }


    /**
     * @return ArrayCollection
     */
    public function getCampTypes() {
        return $this->campTypes;
    }
}
