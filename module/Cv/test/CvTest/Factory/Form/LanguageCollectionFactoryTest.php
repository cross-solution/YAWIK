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
use Cv\Entity\Language;
use Cv\Factory\Form\LanguageSkillCollectionFactory;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Cv\Factory\Form\LanguageSkillCollectionFactory
 * 
 * @covers \Cv\Factory\Form\LanguageSkillCollectionFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class LanguageSkillCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = LanguageSkillCollectionFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokation()
    {
        $container = $this->target->__invoke(new ServiceManager(),'irrelevant');

        $this->assertInstanceOf(CollectionContainer::class, $container);
        $this->assertAttributeEquals('Cv/LanguageSkillForm', 'formService', $container);
        $this->assertAttributeInstanceOf(Language::class, 'newEntry', $container);
        $this->assertEquals('Additional Language Skills', $container->getLabel());
    }
    
}