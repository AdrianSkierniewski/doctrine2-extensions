<?php namespace Gzero\Doctrine2Tree\Entity;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeNode
 *
 * @package    Gzero\Doctrine2Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
interface TreeNode {

    public function getId();

    public function getPath();

    public function setAsRoot();

    public function getLevel();

    public function getParent();

    public function getChildren();

    public function calculatePath();

} 
