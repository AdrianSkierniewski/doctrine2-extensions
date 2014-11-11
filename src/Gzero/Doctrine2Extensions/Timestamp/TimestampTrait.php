<?php namespace Gzero\Doctrine2Extensions\Timestamp;

use Doctrine\ORM\Mapping as ORM;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Trait Timestamp - To use this trait you must set HasLifecycleCallbacks on your entity
 *
 * @package    Gzero\Doctrine2Extensions\Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TimestampTrait {

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Tell doctrine that before we persist or update we call the updateTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        $this->updatedAt = new \DateTime(date('Y-m-d H:i:s'));
        if ($this->getCreatedAt() == NULL) {
            $this->createdAt = new \DateTime(date('Y-m-d H:i:s'));
        }
    }
} 
