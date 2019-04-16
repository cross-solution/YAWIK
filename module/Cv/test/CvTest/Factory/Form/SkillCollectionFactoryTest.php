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
use Cv\Entity\Skill;
use Cv\Factory\Form\SkillCollectionFactory;
use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Cv\Factory\Form\SkillCollectionFactory
 *
 * @covers \Cv\Factory\Form\SkillCollectionFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class SkillCollectionFactoryTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = SkillCollectionFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokation()
    {
        $container = $this->target->__invoke(new ServiceManager(), 'irrelevant');

        $this->assertInstanceOf(CollectionContainer::class, $container);
        $this->assertAttributeEquals('CvSkillForm', 'formService', $container);
        $this->assertAttributeInstanceOf(Skill::class, 'newEntry', $container);
        $this->assertEquals('Skills', $container->getLabel());
    }
}
