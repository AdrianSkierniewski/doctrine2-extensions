<?php namespace fixtures\Doctrine2Test;

use Gzero\Doctrine2Tree\Entity\TreeNode;
use Gzero\Doctrine2Tree\Entity\TreeTrait;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class Tree
 *
 * @package    fixtures\Doctrine2Test
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 * @Entity
 */
class Tree2 implements TreeNode {

    use TreeTrait;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

} 
