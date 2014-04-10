<?php namespace Gzero\Doctrine2Tree\Entity;

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
     * @Column(type="string", nullable=TRUE)
     * @var string
     */
    protected $path;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $level = 0;

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * TreeSubscriber
     */

    protected $parent;

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

    public function setAsRoot()
    {
        $this->parent = NULL;
        $this->level  = 0;
        if ($this->getId()) {
            $this->path = $this->getId() . '/';
        }
    }

    public function setAsChildren(TreeNode $node)
    {
        $this->parent = $node->getParent();
        $this->level  = $node->getLevel();
        $this->path   = $node->getPath() . $this->getId() . '/';

    }

    /**
     * @return self
     */
    public function getParent()
    {
        return $this->parent;
    }

}
