<?php namespace Gzero\Doctrine2Tree\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Gzero\Doctrine2Tree\Entity\TreeNode;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeTrait
 *
 * @package    Gzero\Doctrine2Tree\Repository
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TreeTrait {

    /**
     * @param \Gzero\Doctrine2Tree\Entity\TreeNode $node
     * @param array                                $orderBy
     *
     * @return mixed
     */
    public function findDescendants(TreeNode $node, array $orderBy = NULL)
    {
        /* @var QueryBuilder $qb */
        $qb = $this->_em->createQueryBuilder();
        $qb->select('n')
            ->from($this->getClassName(), 'n')
            ->where('n.path LIKE :path')
            ->setParameter('path', $node->getChildrenPath() . '%')
            ->orderBy('n.level');
        return $qb->getQuery()->getResult();

    }
} 
