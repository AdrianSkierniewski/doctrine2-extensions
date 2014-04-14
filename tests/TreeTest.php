<?php
use fixtures\Doctrine2Test\Tree;
use fixtures\Doctrine2Test\Tree2;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeTest
 *
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
class TreeTest extends Doctrine2TestCase {

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function is_instantiable()
    {
        $this->assertInstanceOf('fixtures\Doctrine2Test\Tree', new Tree());
    }

    /**
     * @test
     */
    public function is_new_node_saved_as_root()
    {
        $node = new Tree();
        $this->em->persist($node);
        $this->em->flush();
        $this->em->refresh($node);
        $this->assertEquals($node->getPath(), '/'); // Path equals id/
        $this->assertNull($node->getParent()); // No parent
        $this->assertEquals($node->getLevel(), 0); // Root level equals 0
    }

    /**
     * @test
     */
    public function can_add_children()
    {
        $root   = new Tree();
        $child  = new Tree();
        $child2 = new Tree();
        $child3 = new Tree();
        $child->setChildOf($root);
        $child2->setChildOf($root);
        $this->assertEquals(2, $root->getChildren()->count(), 'Root should have 2 children before persist');
        $child3->setChildOf($child2);
        $this->assertEquals(1, $child2->getChildren()->count(), 'Child2 should have 1 child before persist');
        $this->em->persist($root);
        $this->em->flush(); // Event fired - postPersist
        $this->assertCount(2, $root->getChildren()); // Root have 2 children
        $this->assertEquals('/1/', $root->getChildren()->first()->getPath()); // Check path
        $this->assertEquals(1, $root->getChildren()->first()->getLevel()); // Check level
    }

    /**
     * @test
     * @expectedException \Gzero\Doctrine2Tree\Entity\TreeException
     */
    public function can_only_add_same_entity()
    {
        $node1 = new Tree();
        $node2 = new Tree2();
        $node1->setChildOf($node2);
    }

    /**
     * @test
     *
     */
    public function can_add_sibling()
    {
        extract($this->_createSimpleTree());
        $sibling1 = new Tree();
        $sibling2 = new Tree();
        /** @noinspection PhpUndefinedVariableInspection */
        $sibling1->setSiblingOf($root); // Another root
        /** @noinspection PhpUndefinedVariableInspection */
        $sibling2->setSiblingOf($child1_1_1);
        $this->em->persist($root);
        $this->em->persist($sibling1);
        $this->em->flush();
        $this->assertNull($sibling1->getParent()); // Sibling for root is root node
        $this->assertEquals($sibling1->getLevel(), 0); // Root level is 0
        $this->assertEquals(3, $sibling2->getLevel()); // Same level as sibling
        $this->assertEquals('/1/2/3/', $sibling2->getPath()); // Same path as sibling
        $this->assertSame($child1_1_1->getParent(), $sibling2->getParent(), 'Sibling should have same parent');
    }

    /**
     * @test
     */
    public function can_move_subtree()
    {
        extract($this->_createSimpleTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($root);
        $this->em->flush();
        /** @noinspection PhpUndefinedVariableInspection */
        $child1->setChildOf($child2);
        $this->em->persist($root);
        $this->em->flush();
        $this->em->persist($root);
        $this->em->flush();
    }

    /**
     * @test
     * @expectedException \Gzero\Doctrine2Tree\Entity\TreeException
     */
    public function cant_move_parent_to_descendant()
    {
        extract($this->_createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($root);
        $this->em->flush();
        /** @noinspection PhpUndefinedVariableInspection */
        $child2_2->setChildOf($child2_2_2);
        $this->em->persist($child2_2);
        $this->em->flush();

    }

    /**
     * @test
     * @TODO check order by level
     * @TODO check descendants nodes
     */
    public function can_find_descendants()
    {
        extract($this->_createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($root);
        $this->em->flush();
        $repo = $this->em->getRepository('fixtures\Doctrine2Test\Tree');
        /* @var \fixtures\Doctrine2Test\TreeRepository $repo */
        $this->assertCount(10, $repo->findDescendants($root));
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertCount(2, $repo->findDescendants($child1));
    }

    /**
     * @test
     * @TODO check order by level
     * @TODO check ancestors nodes
     */
    public function can_find_ancestors()
    {
        extract($this->_createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($root);
        $this->em->flush();
        /* @var \fixtures\Doctrine2Test\TreeRepository $repo */
        $repo = $this->em->getRepository('fixtures\Doctrine2Test\Tree');
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertCount(3, $repo->findAncestors($child1_1_1));
        $this->assertCount(0, $repo->findAncestors($root), "Root shouldn't have ancestors");
    }

    /**
     * Helper function
     *
     * @return array
     */
    protected function _createSimpleTree()
    {
        $tree               = array();
        $tree['root']       = (new Tree())->setAsRoot(); // id 1 path / level 0
        $tree['child1']     = (new Tree())->setChildOf($tree['root']); // id 2 path /1/ level 1
        $tree['child1_1']   = (new Tree())->setChildOf($tree['child1']); // id 3 path /1/2/ level 2
        $tree['child1_1_1'] = (new Tree())->setChildOf($tree['child1_1']); // id 4 /1/2/3/ level 3
        $tree['child2']     = (new Tree())->setChildOf($tree['root']); // id 5 path /1/ level 1
        $tree['child3']     = (new Tree())->setChildOf($tree['root']); // id 6 path /1/ level 1
        return $tree;
    }

    /**
     * Helper function
     *
     * @return array
     */
    protected function _createAdvancedTree()
    {
        $tree                 = array();
        $tree['root']         = (new Tree())->setAsRoot();
        $tree['child1']       = (new Tree())->setChildOf($tree['root']);
        $tree['child2']       = (new Tree())->setChildOf($tree['root']);
        $tree['child3']       = (new Tree())->setChildOf($tree['root']);
        $tree['child1_1']     = (new Tree())->setChildOf($tree['child1']);
        $tree['child1_1_1']   = (new Tree())->setChildOf($tree['child1_1']);
        $tree['child2_1']     = (new Tree())->setChildOf($tree['child2']);
        $tree['child2_2']     = (new Tree())->setChildOf($tree['child2']);
        $tree['child2_2_1']   = (new Tree())->setChildOf($tree['child2_2']);
        $tree['child2_2_2']   = (new Tree())->setChildOf($tree['child2_2']);
        $tree['child2_2_2_1'] = (new Tree())->setChildOf($tree['child2_2_2']);
        return $tree;
    }
}
