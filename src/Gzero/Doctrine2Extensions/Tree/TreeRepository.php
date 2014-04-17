<?php namespace Gzero\Doctrine2Extensions\Tree;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Interface TreeRepository
 *
 * @package    Gzero\Doctrine2Extensions\Repository
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
interface TreeRepository {

    public function findAncestors(TreeNode $node);

    public function findDescendants(TreeNode $node);

    public function findChildren(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL);

    public function findSiblings(TreeNode $node, array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL);

} 
