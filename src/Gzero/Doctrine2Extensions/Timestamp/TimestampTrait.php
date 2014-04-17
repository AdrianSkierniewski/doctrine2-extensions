<?php namespace Gzero\Entity\Traits;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Trait Timestamp - To use this trait you must set @HasLifecycleCallbacks on your entity
 *
 * @package    Gzero\Doctrine2Extensions\Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TimestampTrait {

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Tell doctrine that before we persist or update we call the updateTimestamps() function.
     *
     * @PrePersist
     * @PreUpdate
     */
    public function updateTimestamps()
    {
        $this->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));
        if ($this->getCreatedAt() == NULL) {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }
} 
