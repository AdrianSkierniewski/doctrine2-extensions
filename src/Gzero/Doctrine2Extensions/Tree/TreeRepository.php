<?php namespace Gzero\Doctrine2Extensions\Tree;

use Doctrine\ORM\Query;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Interface TreeRepository
 *
 * @package    Gzero\Doctrine2Extensions\Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
interface TreeRepository {

    /**
     * Get all ancestors nodes to specific node
     *
     * @param TreeNode $node
     * @param int      $hydrate
     *
     * @return mixed
     */
    public function getAncestors(TreeNode $node, $hydrate = Query::HYDRATE_ARRAY);

    /**
     * Get all descendants nodes to specific node
     *
     * @param TreeNode $node
     * @param bool     $tree If you want get in tree structure instead of list
     * @param int      $hydrate
     *
     * @return mixed
     */
    public function getDescendants(TreeNode $node, $tree = FALSE, $hydrate = Query::HYDRATE_ARRAY);

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
    public function getChildren(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL);

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
    public function getSiblings(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL);

} 
