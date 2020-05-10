<?php

declare(strict_types=1);

namespace eCamp\Lib\Entity;

use DateTime;
use \Doctrine\Common\Util\ClassUtils;
use \Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity {

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", nullable=false)
     */
    protected $id;

    /**
     * @var DateTime
     * @ORM\Column(name="create_time", type="datetime")
     */
    protected $createTime;

    /**
     * @var DateTime
     * @ORM\Column(name="update_time", type="datetime")
     */
    protected $updateTime;


    public function __construct() {
        $this->id = base_convert(crc32(uniqid()), 10, 16);

        $this->createTime = new DateTime();
        $this->createTime->setTimestamp(0);

        $this->updateTime = new DateTime();
        $this->updateTime->setTimestamp(0);
    }


    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist() {
        $this->createTime = new DateTime();
        $this->updateTime = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate() {
        $this->updateTime = new DateTime();
    }

    public function __toString() {
        return "[" . $this->getClassname() . "::" . $this->getId() . "]";
    }

    private function getClassname() {
        return ClassUtils::getClass($this);
    }
}
