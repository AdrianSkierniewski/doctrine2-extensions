<?php namespace Gzero\Doctrine2Extensions\Tree;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeSubscriber
 *
 * @package    Gzero\Doctrine2Extensions\Tree
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

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof TreeNode) {
            $parent = $entity->getParent();
            if ($parent) { // We are not persisting root
                $em = $eventArgs->getEntityManager();
                if ($entity->getPath() != $entity->calculatePath()) { // We`re persisting for not persisted entity
                    $entity->setPath($entity->calculatePath());
                    $em->persist($entity);
                    $em->flush();
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof TreeNode) {
            if ($eventArgs->hasChangedField('parent')) {
                $this->validateParentMove($entity, $eventArgs);
                $this->updateChildren($entity, $eventArgs, $eventArgs->getEntityManager());
            }
        }
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
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

    /**
     * Updating children after parent move
     *
     * @param TreeNode           $entity
     * @param LifecycleEventArgs $eventArgs
     * @param EntityManager      $em
     */
    private function updateChildren(TreeNode $entity, LifecycleEventArgs $eventArgs, EntityManager $em)
    {
        $descendantsPath    = $eventArgs->getOldValue('path') . $entity->getId() . '/';
        $newDescendantsPath = $eventArgs->getNewValue('path') . $entity->getId() . '/';
        $query              = $em->createQuery(
            "SELECT n.id, n.path FROM " . get_class($entity) . " n
                    WHERE n.path LIKE '" . $descendantsPath . "%' ORDER BY n.level ASC"
        );
        $descendants        = $query->getResult(); // List all descendants our entity
        foreach ($descendants as $descendant) {
            $path  = preg_replace("|^$descendantsPath|", $newDescendantsPath, $descendant['path']);
            $query = $em->createQuery(
                "UPDATE " . get_class($entity) . " n SET n.path = '" . $path . "'
                WHERE n.id = " . $descendant['id']
            );
            $query->getResult(); // updating descendant path
        }
    }

    /**
     * @param $classMetadata
     *
     * @return bool
     */
    private function hasTreeTrait($classMetadata)
    {
        return in_array(
            'Gzero\Doctrine2Extensions\Tree\TreeNodeTrait',
            array_keys($classMetadata->reflClass->getTraits()),
            TRUE
        );
    }

    /**
     * @param TreeNode           $entity
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws TreeException
     */
    private function validateParentMove(TreeNode $entity, LifecycleEventArgs $eventArgs)
    {
        if (preg_match('|\/' . $entity->getId() . '\/|', $eventArgs->getNewValue('path'))) {
            throw new TreeException('Illegal parent move');
        }
    }

}
