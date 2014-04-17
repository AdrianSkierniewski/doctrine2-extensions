<?php namespace fixtures\Doctrine2Test;

use Doctrine\ORM\EntityRepository;
use Gzero\Doctrine2Extensions\Tree\TreeRepository as TreeRepositoryInterface;
use Gzero\Doctrine2Extensions\Tree\TreeRepositoryTrait;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class TreeRepository
 *
 * @package    fixtures\Doctrine2Test
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
class TreeRepository extends EntityRepository implements TreeRepositoryInterface {

    use TreeRepositoryTrait;
} 
