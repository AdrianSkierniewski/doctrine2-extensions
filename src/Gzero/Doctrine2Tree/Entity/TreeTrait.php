<?php namespace Gzero\Doctrine2Tree\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Trait Tree
 *
 * @package    Gzero\Doctrine2Tree
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
trait TreeTrait {

    /**
     * @Column(type="string")
     * @var string
     */
    protected $path = '/';

    /**
     * @Column(type="integer")
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
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

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

    public function calculatePath()
    {
        if ($this->getParent()) {
            return $this->getParent()->getPath() . $this->getParent()->getId() . '/';
        }
        return '/';
    }

    public function calculateLevel()
    {
        if ($this->getParent()) {
            return $this->getParent()->getLevel() + 1;
        }
        return 0;
    }

    /**
     * @param TreeNode $node
     *
     * @throws TreeException
     */
    protected function isSameClass(TreeNode $node)
    {
        if (get_class($this) != get_class($node)) {
            throw new TreeException('Nodes must be same entity: ' . get_class($this) . ' not equals ' . get_class($node));
        }
    }

}
