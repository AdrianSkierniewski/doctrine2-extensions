<?php namespace Gzero\Doctrine2Tree\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Gzero\Doctrine2Tree\Entity\TreeNode;

/**
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

    /**
     * @param TreeNode $node
     *
     * @return array
     */
    public function findAncestors(TreeNode $node)
    {
        /* @var QueryBuilder $qb */
        if ($node->getPath() != '/') { // root does not have ancestors
            $ancestorsIds = explode('/', substr(substr($node->getPath(), 1), 0, -1));
            $qb           = $this->_em->createQueryBuilder();
            $qb->select('n')
                ->from($this->getClassName(), 'n')
                ->where('n.id IN(:ids)')
                ->setParameter('ids', $ancestorsIds)
                ->orderBy('n.level');
            return $qb->getQuery()->getResult();
        }
        return [];
    }

    /**
     * @param TreeNode $node
     * @param array    $criteria
     * @param array    $orderBy
     * @param null     $limit
     * @param null     $offset
     *
     * @return
     */
    public function findChildren(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return parent::findBy(array_merge($criteria, ['path' => $node->getChildrenPath()]), $orderBy, $limit, $offset);
    }

    /**
     * @param TreeNode $node
     * @param array    $criteria
     * @param array    $orderBy
     * @param null     $limit
     * @param null     $offset
     *
     * @return mixed
     */
    public function findSiblings(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        $siblings = parent::findBy(array_merge($criteria, ['path' => $node->getPath()]), $orderBy, $limit, $offset);
        return array_filter( // skip $node
            $siblings,
            function ($var) use ($node) {
                return $var->getId() != $node->getId();
            }
        );
    }

} 
