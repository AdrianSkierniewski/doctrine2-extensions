<?php
use fixtures\Doctrine2Test\Tree;

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
}
