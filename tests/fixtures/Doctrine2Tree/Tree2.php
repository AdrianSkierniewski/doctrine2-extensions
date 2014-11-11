<?php namespace fixtures\Doctrine2Test;

use Gzero\Doctrine2Extensions\Tree\TreeNode;
use Gzero\Doctrine2Extensions\Tree\TreeNodeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class Tree
 *
 * @package    fixtures\Doctrine2Test
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 * @ORM\Entity
 */
class Tree2 implements TreeNode {

    use TreeNodeTrait;

    /**
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
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
