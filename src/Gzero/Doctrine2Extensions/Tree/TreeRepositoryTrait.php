<?php namespace Gzero\Doctrine2Extensions\Tree;

use Doctrine\ORM\Query;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeTrait - This is only EXAMPLE IMPLEMENTATION TRAIT!
 *
 * @package    Gzero\Doctrine2Extensions\Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TreeRepositoryTrait {

    /**
     * Get all descendants nodes to specific node
     *
     * @param TreeNode $node
     * @param bool     $tree If you want get in tree structure instead of list
     * @param int      $hydrate
     *
     * @return mixed
     */
    public function getDescendants(TreeNode $node, $tree = FALSE, $hydrate = Query::HYDRATE_ARRAY)
    {
        $qb = $this->newQB()
            ->from($this->getClassName(), 'n')
            ->where('n.path LIKE :path')
            ->setParameter('path', $node->getChildrenPath() . '%')
            ->orderBy('n.level');
        if ($tree) {
            $qb->select('n', 'c', 'p')
                ->leftJoin('n.parent', 'p')
                ->leftJoin('n.children', 'c');
        } else {
            $qb->select('n');
        }
        $nodes = $qb->getQuery()->getResult($hydrate);
        if ($tree) {
            return array_filter(
                $nodes,
                function ($item) use ($node) { // We return children's array because we don't have one root
                    $level = (is_array($item)) ? @$item['level'] : $item->getLevel(); // @TODO Ugly HAX ['level']
                    return ($level == $node->getLevel() + 1);
                }
            );
        } else {
            return $nodes;
        }
    }

    /**
     * Get all ancestors nodes to specific node
     *
     * @param TreeNode $node
     * @param int      $hydrate
     *
     * @return mixed
     */
    public function getAncestors(TreeNode $node, $hydrate = Query::HYDRATE_ARRAY)
    {
        if ($node->getPath() != '/') { // root does not have ancestors
            $ancestorsIds = $node->getAncestorsIds(); //

            $qb = $this->newQB()
                ->select('n')
                ->from($this->getClassName(), 'n')
                ->where('n.id IN(:ids)')
                ->setParameter('ids', $ancestorsIds)
                ->orderBy('n.level');

            $nodes = $qb->getQuery()->getResult($hydrate);
            return $nodes;
        }
        return [];
    }

    /**
     * Get all children nodes to specific node
     *
     * @param TreeNode $node
     * @param array    $criteria
     * @param array    $orderBy
     * @param null     $limit
     * @param null     $offset
     *
     * @return mixed
     */
    public function getChildren(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return parent::findBy(array_merge($criteria, ['path' => $node->getChildrenPath()]), $orderBy, $limit, $offset);
    }

    /**
     * Get all siblings nodes to specific node
     *
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
