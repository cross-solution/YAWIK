<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace CoreTest\Form\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\Form\View\Helper\FormFileUpload as FileUploadHelper;
use Core\Form\Element\FileUpload as FileUploadElement;
use Core\Entity\FileInterface as FileEntity;
use Zend\View\Renderer\PhpRenderer as View;
use Zend\I18n\Translator\TranslatorInterface as Translator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @coversDefaultClass Core\Form\View\Helper\FormFileUpload
 */
class FormFileUploadTest extends TestCase
{

    /**
     * @var FileUploadHelper
     */
    protected $fileUploadHelper;
    
    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $view = new View();
        $view->plugin('basepath')->setBasePath('');
        
        $translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        $translator->method('translate')
            ->willReturnArgument(0);
        
        $this->fileUploadHelper = new FileUploadHelper();
        $this->fileUploadHelper->setView($view);
        $this->fileUploadHelper->setTranslator($translator);
    }
    
    /**
     * @covers ::render
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expects element of type
     */
    public function testRenderThrowsInvalidArgumentException()
    {
        $element = $this->getMockBuilder(\Zend\Form\ElementInterface::class)
            ->getMock();
        
        $this->fileUploadHelper->render($element);
    }
    
    /**
     * @covers ::renderFileList
     * @covers ::setupAssets
     */
    public function testRenderFileListSingle()
    {
        $entity = $this->getMockBuilder(FileEntity::class)
            ->getMock();
        
        $element = $this->getMockBuilder(FileUploadElement::class)
            ->setConstructorArgs(['elementName'])
            ->setMethods(['getFileEntity'])
            ->getMock();
        $element->method('getFileEntity')
            ->willReturn($entity);
        
        $result = $this->fileUploadHelper->renderFileList($element);
        $this->assertIsString($result);
        
        return $element;
    }
    
    /**
     * @covers ::renderFileList
     * @covers ::setupAssets
     */
    public function testRenderFileListMultiple()
    {
        $entity = $this->getMockBuilder(FileEntity::class)
            ->getMock();
        
        $collection = new ArrayCollection([$entity]);
        
        $element = $this->getMockBuilder(FileUploadElement::class)
            ->setConstructorArgs(['elementName'])
            ->setMethods(['getFileEntity'])
            ->getMock();
        $element->method('getFileEntity')
            ->willReturn($collection);
        $element->setIsMultiple(true);
        
        $result = $this->fileUploadHelper->renderFileList($element);
        $this->assertIsString($result);
    }
    
    /**
     * @covers ::renderFileElement
     * @expectedException \DomainException
     * @expectedExceptionMessage requires that the element has an assigned name
     */
    public function testRenderFileElementThrowsDomainException()
    {
        $this->fileUploadHelper->renderFileElement(new FileUploadElement());
    }
    
    /**
     * @covers ::renderFileElement
     */
    public function testRenderFileElement()
    {
        $element = new FileUploadElement('elementName');
        
        $result = $this->fileUploadHelper->renderFileElement($element);
        $this->assertIsString($result);
        $this->assertStringStartsWith('<input', $result);
        $this->assertContains('type="file"', $result);
        $this->assertContains('name="' . $element->getName() . '"', $result);
    }
    
    /**
     * @covers ::getDropZoneClass
     */
    public function testGetDropZoneClass()
    {
        $element = new FileUploadElement();
        $result = $this->fileUploadHelper->getDropZoneClass($element);
        $this->assertIsString($result);
        $this->assertContains('single', $result);
        $this->assertNotContains('multiple', $result);
        $this->assertNotContains('fu-non-clickable', $result);
        
        $element->setIsMultiple(true);
        $result = $this->fileUploadHelper->getDropZoneClass($element);
        $this->assertNotContains('single', $result);
        $this->assertContains('multiple', $result);
    }
    
    /**
     * @covers ::setEmptyNotice
     * @depends testRenderFileListSingle
     */
    public function testSetEmptyNotice(FileUploadElement $element)
    {
        $emptyNotice = 'some empty notice';
        $this->assertSame($this->fileUploadHelper, $this->fileUploadHelper->setEmptyNotice($emptyNotice));
        $this->assertContains($emptyNotice, $this->fileUploadHelper->renderFileList($element));
    }
    
    /**
     * @covers ::getEmptyNotice
     * @covers ::getNonEmptyNotice
     * @covers ::getDefaultNotice
     * @depends testRenderFileListSingle
     */
    public function testGetDefaultNotice(FileUploadElement $element)
    {
        $this->assertContains('Click here to add files', $this->fileUploadHelper->renderFileList($element));
    }
    
    /**
     * @covers ::setNonEmptyNotice
     * @depends testRenderFileListSingle
     */
    public function testSetNonEmptyNotice(FileUploadElement $element)
    {
        $nonEmptyNotice = 'some non empty notice';
        $this->assertSame($this->fileUploadHelper, $this->fileUploadHelper->setNonEmptyNotice($nonEmptyNotice));
        $this->assertContains($nonEmptyNotice, $this->fileUploadHelper->renderFileList($element));
    }
    
    /**
     * @covers ::setAllowRemove
     * @covers ::renderFileList
     * @depends testRenderFileListSingle
     */
    public function testSetAllowRemove(FileUploadElement $element)
    {
        $this->assertContains('fu-delete-button', $this->fileUploadHelper->renderFileList($element));
        
        $this->assertSame($this->fileUploadHelper, $this->fileUploadHelper->setAllowRemove(false));
        $this->assertNotContains('fu-delete-button', $this->fileUploadHelper->renderFileList($element));
    }
    
    /**
     * @covers ::setAllowClickableDropZone
     * @covers ::getDropZoneClass
     */
    public function testSetAllowClickableDropZone()
    {
        $expected = 'fu-non-clickable';
        $element = new FileUploadElement();
        $this->assertNotContains($expected, $this->fileUploadHelper->getDropZoneClass($element));
        $this->assertSame($this->fileUploadHelper, $this->fileUploadHelper->setAllowClickableDropZone(false));
        $this->assertContains($expected, $this->fileUploadHelper->getDropZoneClass($element));
    }
    
    /**
     * @covers ::render
     * @covers ::renderMarkup
     */
    public function testRender()
    {
        $element = new FileUploadElement('elementName');
        $markupOutput = 'markup output';
        $fileElementOutput = 'fileElement output';
        $fileElementPlaceholder = '__input__';
        
        $fileUploadHelper = $this->getMockBuilder(FileUploadHelper::class)
            ->setMethods(['renderMarkup', 'renderFileElement'])
            ->getMock();
        $fileUploadHelper->expects($this->once())
            ->method('renderMarkup')
            ->with($this->equalTo($element))
            ->willReturn($markupOutput . $fileElementPlaceholder);
        $fileUploadHelper->expects($this->once())
            ->method('renderFileElement')
            ->with($this->equalTo($element))
            ->willReturn($fileElementOutput);
        
        $result = $fileUploadHelper->render($element);
        $this->assertIsString($result);
        $this->assertContains($markupOutput, $result);
        $this->assertContains($fileElementOutput, $result);
        $this->assertNotContains($fileElementPlaceholder, $result);
        
        $result = $this->fileUploadHelper->render($element);
        $this->assertIsString($result);
        $this->assertNotContains($fileElementPlaceholder, $result);
    }
}
