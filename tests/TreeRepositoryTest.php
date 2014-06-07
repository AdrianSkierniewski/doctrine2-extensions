<?php
use Doctrine\ORM\Query;

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeRepositoryTest
 *
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
class TreeRepositoryTest extends Doctrine2TestCase {

    /* @var \fixtures\Doctrine2Test\TreeRepository */
    protected $repo;

    public function setUp()
    {
        parent::setUp();
        $this->repo = $this->em->getRepository('fixtures\Doctrine2Test\Tree');
    }

    /**
     * @test
     */
    public function is_instantiable()
    {
        $this->assertInstanceOf(
            'fixtures\Doctrine2Test\TreeRepository',
            $this->em->getRepository('fixtures\Doctrine2Test\Tree')
        );
    }

    /**
     * @test
     * @TODO check order by level
     * @TODO check descendants nodes
     */
    public function can_get_descendants()
    {
        extract($this->createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertCount(10, $this->repo->getDescendants($root));
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertCount(2, $this->repo->getDescendants($child1));
    }

    /**
     * @test
     * @TODO check order by level
     */
    public function can_get_ancestors()
    {
        extract($this->createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $ancestors = $this->repo->getAncestors($child1_1_1);
        $this->assertCount(3, $ancestors);
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertEquals($root->getId(), $ancestors[0]['id']);
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertEquals($child1->getId(), $ancestors[1]['id']);
        $ancestors = $this->repo->getAncestors($child1_1_1, Query::HYDRATE_OBJECT);
        $this->assertInstanceOf('fixtures\Doctrine2Test\Tree', $ancestors[0]);
        $this->assertEquals($ancestors[0]->getId(), $child1_1_1->getParent()->getParent()->getParent()->getId());
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertCount(0, $this->repo->getAncestors($root), "Root shouldn't have ancestors");
    }

    /**
     * @test
     */
    public function can_get_children()
    {
        extract($this->createAdvancedTree());
        /** @noinspection PhpUndefinedVariableInspection */
        $children = $this->repo->getChildren($child1_1);
        $this->assertCount(1, $children);
        $this->assertEquals($children[0]->getPath(), $child1_1->getChildrenPath());
    }

}
