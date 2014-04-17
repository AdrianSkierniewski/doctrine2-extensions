<?php
use fixtures\Doctrine2Test\Tree;
use fixtures\Doctrine2Test\Tree2;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeTest
 *
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
class TreeNodeTest extends Doctrine2TestCase {

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
     * @expectedException \Gzero\Doctrine2Extensions\Tree\TreeException
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
        extract($this->createSimpleTree());
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
        extract($this->createSimpleTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $child1->setChildOf($child2);
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($root);
        $this->em->flush();
        $this->em->persist($root);
        $this->em->flush();
    }

    /**
     * @test
     * @expectedException \Gzero\Doctrine2Extensions\Tree\TreeException
     */
    public function cant_move_parent_to_descendant()
    {
        extract($this->createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $child2_2->setChildOf($child2_2_2);
        $this->em->persist($child2_2);
        $this->em->flush();
    }

}
