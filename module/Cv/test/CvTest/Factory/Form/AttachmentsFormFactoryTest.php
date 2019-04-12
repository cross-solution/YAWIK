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

use Core\Form\Element\FileUpload;
use Core\Form\FileUploadFactory;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Factory\Form\AttachmentsFormFactory;
use Cv\Options\ModuleOptions;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Cv\Factory\Form\AttachmentsFormFactory
 *
 * @covers \Cv\Factory\Form\AttachmentsFormFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class AttachmentsFormFactoryTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    private $target = [
        AttachmentsFormFactoryMock::class,
        '@testInheritance' => AttachmentsFormFactory::class,
        '@testDefaultPropertyValues' => AttachmentsFormFactory::class,
    ];

    private $inheritance = [ FileUploadFactory::class ];

    private $attributes = [
        'fileElement' => 'Core/FileUpload',
        'fileName'    => 'attachments',
        'fileEntityClass' => '\Cv\Entity\Attachment',
        'multiple' => true,
        'options' => 'Cv/Options'
    ];

    public function testThrowsExceptionIfInvalidOptionsAreProvided()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('$options must be instance of');

        $options = $this->getMockBuilder('\Zend\Stdlib\AbstractOptions')->getMockForAbstractClass();
        $this->target->__options__ = $options;

        $this->target->createService(new ServiceManager());
    }

    public function provideTypes()
    {
        return [
            [ null ],
            [ ['text/plain'] ],
        ];
    }

    /**
     * @dataProvider provideTypes
     *
     * @param $types
     */
    public function testConfiguresForm($types)
    {
        $options = $this
            ->getMockBuilder(ModuleOptions::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttachmentsMaxSize', 'getAttachmentsMimeType', 'getAttachmentsCount'])
            ->getMock();

        $size = 5000000;
        $count = 5;
        $options->expects($this->once())->method('getAttachmentsMaxSize')->willReturn(5000000);
        $options->expects($this->once())->method('getAttachmentsMimeType')->willReturn($types);
        $options->expects($this->once())->method('getAttachmentsCount')->willReturn($count);

        $file = $this
            ->getMockBuilder(FileUpload::class)
            ->disableOriginalConstructor()
            ->setMethods(['setMaxSize', 'setAllowedTypes', 'setMaxFileCount', 'setForm'])
            ->getMock();

        $file->expects($this->once())->method('setMaxSize')->with($size);
        if ($types) {
            $file->expects($this->once())->method('setAllowedTypes')->with($types);
        } else {
            $file->expects($this->never())->method('setAllowedTypes');
        }
        $file->expects($this->once())->method('setMaxFileCount')->with($count);

        $form = $this
            ->getMockBuilder('\Core\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(['setIsDisableCapable', 'setIsDisableElementsCapable',
                          'setIsDescriptionsEnabled', 'setDescription', 'setParam',
                          'setLabel', 'get'])
            ->getMock();

        $form->expects($this->once())->method('setIsDisableCapable')->with(false)->will($this->returnSelf());
        $form->expects($this->once())->method('setIsDisableElementsCapable')->with(false)->will($this->returnSelf());
        $form->expects($this->once())->method('setIsDescriptionsEnabled')->with(true)->will($this->returnSelf());
        $form->expects($this->once())->method('setDescription')->with($this->stringStartsWith('Attach images or PDF Documents to your CV'))->will($this->returnSelf());
        $form->expects($this->once())->method('setParam')->with('return', 'file-uri')->will($this->returnSelf());
        $form->expects($this->once())->method('setLabel')->with('Attachments')->will($this->returnSelf());
        $form->expects($this->once())->method('get')->with('attachments')->willReturn($file);

        $file->expects($this->once())->method('setForm')->with($form);

        $this->target->__options__ = $options;
        $this->target->__form__    = $form;

        $this->target->createService(new ServiceManager());
    }
}

class AttachmentsFormFactoryMock extends AttachmentsFormFactory
{
    public $__form__;
    public $__options__;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->configureForm($this->__form__, $this->__options__);
    }
}
