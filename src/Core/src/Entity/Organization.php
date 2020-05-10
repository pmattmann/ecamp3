<?php

declare(strict_types=1);

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="organizations")
 */
class Organization extends BaseEntity {

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;


    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

}
