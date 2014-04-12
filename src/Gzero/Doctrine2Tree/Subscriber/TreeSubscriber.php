<?php namespace Gzero\Doctrine2Tree\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Util\Debug;
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
            Events::preUpdate,
            Events::loadClassMetadata
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        /** @var TreeNode $entity */
        $entity = $eventArgs->getEntity();
        if (!preg_match('/\d\/$/', $entity->getPath())) { // if path no ends with number and /
            $em     = $eventArgs->getEntityManager();
            $parent = $entity->getParent();
            if ($parent) {
                $entity->setPath($parent->getPath() . $entity->getId() . '/');
            } else { // Only root has no parent
                $entity->setAsRoot();
            }
            $em->persist($entity);
            $em->flush();
        }
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
//        Debug::dump($entity);
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if ($this->hasTreeTrait($classMetadata)) {
            $classMetadata->mapManyToOne(
                [
                    'fieldName'    => 'parent',
                    'targetEntity' => $classMetadata->name,
                    'cascade'      => ['persist'],
                    'joinColumns'  => [
                        [
                            'onDelete' => 'CASCADE',
                        ]
                    ]
                ]
            );
            $classMetadata->mapOneToMany(
                [
                    'fieldName'    => 'children',
                    'targetEntity' => $classMetadata->name,
                    'mappedBy'     => 'parent',
                    'cascade'      => ['persist'],
                ]
            );
        }
    }

    private function hasTreeTrait($classMetadata)
    {
        return in_array('Gzero\Doctrine2Tree\Entity\TreeTrait', array_keys($classMetadata->reflClass->getTraits()), TRUE);
    }
}
