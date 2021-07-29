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

use PHPUnit\Framework\TestCase;

use Core\Form\CollectionContainer;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Education;
use Cv\Factory\Form\EducationCollectionFactory;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Tests for \Cv\Factory\Form\EducationCollectionFactory
 *
 * @covers \Cv\Factory\Form\EducationCollectionFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class EducationCollectionFactoryTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = EducationCollectionFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokation()
    {
        $sm = new ServiceManager();
        $container = $this->target->__invoke($sm, 'irrelevant');

        $this->assertInstanceOf(CollectionContainer::class, $container);
        $this->assertAttributeEquals('CvEducationForm', 'formService', $container);
        $this->assertAttributeInstanceOf(Education::class, 'newEntry', $container);
        $this->assertEquals('Education history', $container->getLabel());
    }
}
