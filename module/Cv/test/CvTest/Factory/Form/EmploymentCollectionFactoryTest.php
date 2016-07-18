<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Factory\Form;

use Core\Form\CollectionContainer;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Employment;
use Cv\Factory\Form\EmploymentCollectionFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Cv\Factory\Form\EmploymentCollectionFactory
 * 
 * @covers \Cv\Factory\Form\EmploymentCollectionFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class EmploymentCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = EmploymentCollectionFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $container = $this->target->createService(new ServiceManager());

        $this->assertInstanceOf(CollectionContainer::class, $container);
        $this->assertAttributeEquals('CvEmploymentForm', 'formService', $container);
        $this->assertAttributeInstanceOf(Employment::class, 'newEntry', $container);
        $this->assertEquals('Employment history', $container->getLabel());
    }
    
}