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
use Cv\Entity\Education;
use Cv\Factory\Form\EducationCollectionFactory;
use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Cv\Factory\Form\EducationCollectionFactory
 * 
 * @covers \Cv\Factory\Form\EducationCollectionFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class EducationCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = EducationCollectionFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $fm=new FormElementManagerV2Polyfill();
        $fm->setServiceLocator(new ServiceManager);
        $container = $this->target->createService($fm);

        $this->assertInstanceOf(CollectionContainer::class, $container);
        $this->assertAttributeEquals('CvEducationForm', 'formService', $container);
        $this->assertAttributeInstanceOf(Education::class, 'newEntry', $container);
        $this->assertEquals('Education history', $container->getLabel());
    }
    
}