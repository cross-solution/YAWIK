<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Form;

use PHPUnit\Framework\TestCase;

use Core\Factory\Form\AbstractCustomizableFieldsetFactory;
use Core\Form\CustomizableFieldsetInterface;
use Core\Options\FieldsetCustomizationOptions;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;

/**
 * Tests for \Core\Factory\Form\AbstractCustomizableFieldsetFactory
 *
 * @covers \Core\Factory\Form\AbstractCustomizableFieldsetFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Form
 */
class AbstractCustomizableFieldsetFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = [
        ConcreteAbstractCustomizableFieldsetFactory::class,
        '@testInheritance' => [
            'class' => AbstractCustomizableFieldsetFactory::class,
            'as_reflection' => true
        ],
        '@testCreateServiceInvokesItselfAndResetsCreationOptions' => [
            'class' => AbstractCustomizableFieldsetFactory::class,
            'mock' => [ '__invoke' ]
        ],
        '@testInvokationThrowsExceptionIfOptionsNameIsNotSpecified' => [
            'class' => ConcreteAbstractCustomizableFieldsetFactoryWithClassName::class
        ],
        '@testInvokationCreatesInstance' => [
            'class' => ConcreteAbstractCustomizableFieldsetFactoryWithConstants::class,
        ],
        '@testInvokationThrowsExceptionIfCreatedInstanceIsNotCustomizable' => [
            'class' => ConcreteAbstractCustomizableFieldsetFactoryWithInvalidClassName::class,
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function testCreationOptions()
    {
        $opts = ['test' => 'works!'];
        $this->target->setCreationOptions($opts);

        $this->assertAttributeEquals($opts, 'options', $this->target);
    }

    public function testCreateServiceInvokesItselfAndResetsCreationOptions()
    {
        $container = $this->getServiceManagerMock();
        $plugins   = $this->getPluginManagerMock($container, 1);

        $this->target->expects($this->once())->method('__invoke');

        $this->target->setCreationOptions(['test' => 'resetworks?']);
        $this->target->createService($plugins);

        $this->assertAttributeEquals(null, 'options', $this->target);
    }

    public function testInvokationThrowsExceptionIfClassNameIsNotSpecified()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('CLASS_NAME" must be non empty');

        $this->target->__invoke($this->getServiceManagerMock(), '');
    }

    public function testInvokationThrowsExceptionIfOptionsNameIsNotSpecified()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('"OPTIONS_NAME" must be non empty');

        $container = $this->getServiceManagerMock();

        $this->target->__invoke($container, '');
    }

    public function testInvokationThrowsExceptionIfCreatedInstanceIsNotCustomizable()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Form or Fieldset instance must implement');

        $this->target->__invoke($this->getServiceManagerMock(), '');
    }

    public function testInvokationCreatesInstance()
    {
        $options = new FieldsetCustomizationOptions();
        $container = $this->getServiceManagerMock(['testOptions' => ['service' => $options, 'count_get' => 1]]);
        
        $instance = $this->target->__invoke($container, '');

        $this->assertSame($options, $instance->getCustomizationOptions());
    }
}

class ConcreteAbstractCustomizableFieldsetFactory extends AbstractCustomizableFieldsetFactory
{
}

class ConcreteAbstractCustomizableFieldsetFactoryWithClassName extends AbstractCustomizableFieldsetFactory
{
    const CLASS_NAME = CACFF_FieldsetMock::class;
}

class ConcreteAbstractCustomizableFieldsetFactoryWithInvalidClassName extends AbstractCustomizableFieldsetFactory
{
    const CLASS_NAME = '\Zend\Form\Fieldset';
}

class ConcreteAbstractCustomizableFieldsetFactoryWithConstants extends ConcreteAbstractCustomizableFieldsetFactoryWithClassName
{
    const OPTIONS_NAME = 'testOptions';
}

class CACFF_FieldsetMock implements CustomizableFieldsetInterface
{
    private $options;
    public function setCustomizationOptions(FieldsetCustomizationOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getCustomizationOptions()
    {
        return $this->options;
    }
}
