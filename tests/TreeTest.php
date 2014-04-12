<?php
use Doctrine\Common\Util\Debug;
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
        $dbNode = $this->em->find('fixtures\Doctrine2Test\Tree', 1);
        $this->assertEquals($dbNode->getPath(), $node->getId() . '/'); // Path equals id/
        $this->assertNull($dbNode->getParent()); // No parent
        $this->assertEquals($dbNode->getLevel(), 0); // Root level equals 0
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
        $this->em->persist($child);
        $this->em->persist($child2);
        $this->em->persist($child3);
        $this->em->flush();
        $this->assertEquals($child2->getLevel(), $child->getLevel(), 'Sibling should have same level'); // Sibling
        $this->assertEquals($child3->getPath(), $child2->getPath() . $child3->getId() . '/');
        $this->assertSame($child3->getParent(), $child2);
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
        extract($this->_createSampleTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($root);
        $this->em->flush();
        $sibling1 = new Tree();
        $sibling2 = new Tree();
        $sibling1->setSiblingOf($root);
        /** @noinspection PhpUndefinedVariableInspection */
        $sibling2->setSiblingOf($child1_1_1);
        $this->em->persist($sibling1);
        $this->em->flush();
        $this->assertNull($sibling1->getParent()); // Sibling for root is root node
        $this->assertEquals($sibling1->getLevel(), 0); // Root level is 0
        $this->assertEquals($child1_1_1->getLevel(), $sibling2->getLevel());
        $this->assertSame($child1_1_1->getParent(), $sibling2->getParent(), 'Sibling should have same parent');
    }

//    /**
//     * @test
//     */
//    public function can_move_subtree()
//    {
//        extract($this->_createSampleTree());
//        /** @noinspection PhpUndefinedVariableInspection */
//        $this->em->persist($root);
//        $this->em->flush();
//        print_r($this->logger->queries);
//        exit;
//
//        /** @noinspection PhpUndefinedVariableInspection */
//        $this->em->refresh($child2);
//        /** @noinspection PhpUndefinedVariableInspection */
//        $child2->setSiblingOf($child1);
//        $this->em->persist($child2);
//        $this->em->flush();
//    }

    /**
     * Helper function
     *
     * @return array
     */
    protected function _createSampleTree()
    {
        $tree               = array();
        $tree['root']       = (new Tree())->setAsRoot();
        $tree['child1']     = (new Tree())->setChildOf($tree['root']);
        $tree['child2']     = (new Tree())->setChildOf($tree['root']);
        $tree['child3']     = (new Tree())->setChildOf($tree['root']);
        $tree['child1_1']   = (new Tree())->setChildOf($tree['child1']);
        $tree['child1_1_1'] = (new Tree())->setChildOf($tree['child1_1']);
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
        $tree['child2_1']     = (new Tree())->setChildOf($tree['child2']);
        $tree['child2_2']     = (new Tree())->setChildOf($tree['child2']);
        $tree['child1_1_1']   = (new Tree())->setChildOf($tree['child1_1']);
        $tree['child2_2_1']   = (new Tree())->setChildOf($tree['child2_2']);
        $tree['child2_2_2']   = (new Tree())->setChildOf($tree['child2_2']);
        $tree['child2_2_2_1'] = (new Tree())->setChildOf($tree['child2_2_2']);
        return $tree;
    }
}
