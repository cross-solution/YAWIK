<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Paginator;

use PHPUnit\Framework\TestCase;

use Core\Paginator\PaginatorFactoryAbstract;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Paginator\PaginatorFactory;

/**
 * Tests for \Cv\Paginator\PaginatorFactory
 *
 * @covers \Cv\Paginator\PaginatorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Paginator
 */
class PaginatorFactoryTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        PaginatorFactory::class,
        '@testSetterAndGetter' => PaginatorFactoryMock::class,
    ];

    private $inheritance = [ PaginatorFactoryAbstract::class ];

    private $properties = [
        ['filter', ['default' => 'Cv/PaginationQuery']],
        ['repository', ['default' => 'Cv/Cv']],
    ];
}

class PaginatorFactoryMock extends PaginatorFactory
{
    public function getFilter()
    {
        return parent::getFilter();
    }

    public function getRepository()
    {
        return parent::getRepository();
    }
}
