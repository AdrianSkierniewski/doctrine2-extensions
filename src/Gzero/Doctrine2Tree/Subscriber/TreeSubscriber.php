<?php namespace Gzero\Doctrine2Tree\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Gzero\Doctrine2Tree\Entity\TreeNode;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeSubscriber
 *
 * @package    Gzero\Doctrine2Tree\Subscriber
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
class TreeSubscriber implements EventSubscriber {

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::loadClassMetadata
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        /** @var TreeNode $entity */
        $entity = $eventArgs->getEntity();
        if (!$entity->getPath()) {
            $em = $eventArgs->getEntityManager();
            $entity->setAsRoot();
            $em->persist($entity);
            $em->flush();
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if ($this->hasTreeTrait($classMetadata)) {
            $classMetadata->mapOneToOne(
                [
                    'targetEntity' => $classMetadata->name,
                    'fieldName'    => 'parent',
                    'cascade'      => ['persist'],
                    'joinColumns'  => [
                        [
                            'onDelete' => 'CASCADE',
                        ]
                    ]
                ]
            );
        }
    }

    private function hasTreeTrait($classMetadata)
    {
        return in_array('Gzero\Doctrine2Tree\Entity\TreeTrait', array_keys($classMetadata->reflClass->getTraits()), TRUE);
    }
}
