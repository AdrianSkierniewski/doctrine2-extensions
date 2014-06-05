<?php namespace Gzero\Doctrine2Extensions\Tree;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeTrait
 *
 * @package    Gzero\Doctrine2Extensions\Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TreeRepositoryTrait {

    /**
     * @param TreeNode $node
     * @param array    $orderBy
     *
     * @return mixed
     */
    public function getDescendants(TreeNode $node, array $orderBy = [])
    {
        $qb = $this->newQB()
            ->select('n', 'c', 'p')
            ->from($this->getClassName(), 'n')
            ->leftJoin('n.parent', 'p', 'ON')
            ->leftJoin('n.children', 'c', 'ON')
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
    public function getAncestors(TreeNode $node)
    {
        if ($node->getPath() != '/') { // root does not have ancestors
            $ancestorsIds = explode('/', substr(substr($node->getPath(), 1), 0, -1));

            $qb = $this->newQB()
                ->select('n', 'c', 'p')
                ->from($this->getClassName(), 'n')
                ->leftJoin('n.parent', 'p', 'ON')
                ->leftJoin('n.children', 'c', 'ON')
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
    public function getChildren(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
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
    public function getSiblings(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
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
