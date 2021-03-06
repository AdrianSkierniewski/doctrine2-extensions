<?php namespace Gzero\Doctrine2Extensions\Tree;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Trait Tree
 *
 * @package    Gzero\Doctrine2Extensions\Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TreeNodeTrait {

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $path = '/';

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $level = 0;

    /**
     * TreeSubscriber
     */
    protected $parent;

    /**
     * TreeSubscriber
     */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return $this
     */
    public function setAsRoot()
    {
        $this->parent = NULL;
        $this->level  = 0;
        $this->path   = '/';
        return $this;
    }

    /**
     * @param TreeNode $node
     *
     * @return $this
     */
    public function setChildOf(TreeNode $node)
    {
        $this->isSameClass($node);
        $this->parent = $node;
        $node->getChildren()->add($this); // Important add to collection
        $this->level = $node->getLevel() + 1;
        $this->path  = $node->getPath() . $node->getId() . '/';
        return $this;
    }

    /**
     * @param TreeNode $node
     *
     * @return $this
     */
    public function setSiblingOf(TreeNode $node)
    {
        $this->isSameClass($node);
        $parent = $node->getParent();
        if ($parent) {
            $this->parent = $parent;
            $parent->getChildren()->add($this); // Important add to collection
            $this->level = $node->getLevel();
            $this->path  = $parent->getPath() . $this->getId() . '/';
        } else {
            $this->setAsRoot();
        }
        return $this;
    }

    public function calculatePath()
    {
        if ($this->getParent()) {
            return $this->getParent()->getPath() . $this->getParent()->getId() . '/';
        }
        return '/';
    }

    //------------------------------------------------------------------------------------------------
    // START: Getters & Setters
    //------------------------------------------------------------------------------------------------


    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getChildrenPath()
    {
        return $this->getPath() . $this->getId() . '/';
    }

    /**
     * @return array
     */
    public function getAncestorsIds()
    {
        return explode('/', substr(substr($this->getPath(), 1), 0, -1));
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return self
     */
    public function getParent()
    {
        return $this->parent;
    }

    //-----------------------------------------------------------------------------------------------
    // END:  Getters & Setters
    //-----------------------------------------------------------------------------------------------

    /**
     * @param TreeNode $node
     *
     * @throws TreeException
     */
    protected function isSameClass(TreeNode $node)
    {
        if (get_class($this) != get_class($node) and !preg_match('/^DoctrineProxies/', get_class($node))) {
            throw new TreeException('Nodes must be same entity: ' . get_class($this) . ' not equals ' . get_class($node));
        }
    }

}
