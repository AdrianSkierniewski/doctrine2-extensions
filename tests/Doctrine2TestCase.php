<?php
/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class Doctrine2TestCase
 *
 * @package    tests\Entity
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use fixtures\Doctrine2Test\Tree;

class Doctrine2TestCase extends \PHPUnit_Framework_TestCase {

    protected $dbParams = [
        'driver' => 'pdo_sqlite',
        'memory' => TRUE
    ];
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\DBAL\Logging\DebugStack
     */
    protected $logger;

    public function setUp()
    {
        $paths        = [__DIR__ . "/fixtures/Doctrine2Tree/"];
        $isDevMode    = TRUE;
        $evm          = new \Doctrine\Common\EventManager();
        $this->logger = new \Doctrine\DBAL\Logging\DebugStack();
        $config       = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $config->setSQLLogger($this->logger);
        $evm->addEventSubscriber(new \Gzero\Doctrine2Extensions\Tree\TreeSubscriber());
        $this->em = EntityManager::create($this->dbParams, $config, $evm);

        $this->generateSchema(); // Build the schema for sqlite

        parent::setUp();
    }

    protected function generateSchema()
    {
        // Get the metadata of entities to create the schema.
        $metadata = $this->getMetadatas();

        if (!empty($metadata)) {
            // Create SchemaTool
            $tool = new SchemaTool($this->em);
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);
//            $tool->updateSchema($metadata);
        } else {
            throw new \Doctrine\DBAL\Schema\SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * Overwrite this method to get specific metadata.
     *
     * @return Array
     */
    protected function getMetadatas()
    {
        return $this->em->getMetadataFactory()->getAllMetadata();
    }

    /**
     * Helper function
     *
     * @return array
     */
    protected function createAdvancedTree()
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
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($tree['root']);
        $this->em->flush();
        return $tree;
    }

    /**
     * Helper function
     *
     * @return array
     */
    protected function createSimpleTree()
    {
        $tree               = array();
        $tree['root']       = (new Tree())->setAsRoot(); // id 1 path / level 0
        $tree['child1']     = (new Tree())->setChildOf($tree['root']); // id 2 path /1/ level 1
        $tree['child1_1']   = (new Tree())->setChildOf($tree['child1']); // id 3 path /1/2/ level 2
        $tree['child1_1_1'] = (new Tree())->setChildOf($tree['child1_1']); // id 4 /1/2/3/ level 3
        $tree['child2']     = (new Tree())->setChildOf($tree['root']); // id 5 path /1/ level 1
        $tree['child3']     = (new Tree())->setChildOf($tree['root']); // id 6 path /1/ level 1
        /** @noinspection PhpUndefinedVariableInspection */
        $this->em->persist($tree['root']);
        $this->em->flush();
        return $tree;
    }

}
