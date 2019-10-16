<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */
namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\Container;
use Core\Form\CollectionContainer;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\Collections\ArrayCollection as Collection;
use Core\Form\Form as CoreForm;
use stdClass;

class CollectionContainerTest extends TestCase
{

    /**
     * @var CollectionContainer
     */
    protected $collectionContainer;

    /**
     * @var string
     */
    protected $formService = 'mockService';

    /**
     * @var stdClass
     */
    protected $newEntry;

    /**
     * @var ServiceLocatorInterface
     */
    protected $formElementManager;

    protected function setUp(): void
    {
        $this->newEntry = $this->getMockBuilder(stdClass::class)
            ->setMethods(['getId'])
            ->getMock();
        $this->formElementManager = $this->getMockBuilder(ServiceLocatorInterface::class)->getMock();
        $this->formElementManager->expects($this->any())
            ->method('get')
            ->with($this->formService)
            ->willReturn(new CoreForm());
        $this->collectionContainer = new CollectionContainer($this->formService, $this->newEntry);
        $this->collectionContainer->setFormElementManager($this->formElementManager);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(\Zend\Form\Element::class, $this->collectionContainer);
        $this->assertInstanceOf(Container::class, $this->collectionContainer);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $entity must be instance of
     */
    public function testSetEntityThrowsException()
    {
        $this->collectionContainer->setEntity('invalid collection');
    }
    
    /**
     * @dataProvider expectRuntimeExceptionWithEntityData
     */
    public function testMethodsThrowRuntimeExceptionWithEntity($method, array $parameters = [])
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity must be set');
        call_user_func_array([$this->collectionContainer, $method], $parameters);
    }
    
    public function testSetEntity()
    {
        $collection = new Collection();
        $collection['first'] = $this->getMockBuilder(stdClass::class)
            ->setMethods(['getId'])
            ->getMock();
        $collection['second'] = $this->getMockBuilder(stdClass::class)
            ->setMethods(['getId'])
            ->getMock();
        
        $this->assertSame($this->collectionContainer, $this->collectionContainer->setEntity($collection));
        
        return $collection;
    }
    
    /**
     * @depends testSetEntity
     */
    public function testGetIterator(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $iterator = $this->collectionContainer->getIterator();
        $this->assertInstanceOf(\Iterator::class, $iterator);
        $this->assertSame(count($collection), count($iterator));
    }
    
    /**
     * @depends testSetEntity
     * @dataProvider expectRuntimeExceptionWithFormData
     */
    public function testMethodsThrowRuntimeExceptionWithForm($method, array $parameters = [], Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);

        $formElementManager = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $formElementManager->expects($this->any())
            ->method('get')
            ->with($this->formService)
            ->willReturn('invalid form');
        
        $this->collectionContainer->setFormElementManager($formElementManager);
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('$form must be instance of');
        call_user_func_array([$this->collectionContainer, $method], $parameters);
    }
    
    /**
     * @depends testSetEntity
     */
    public function testCount(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $this->assertSame(count($collection), $this->collectionContainer->count());
    }
    
    /**
     * @depends testSetEntity
     */
    public function testGetFormNonExistent(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $this->assertNull($this->collectionContainer->getForm('non-existent'));
    }
    
    /**
     * @depends testSetEntity
     */
    public function testGetFormExistent(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $entry = $collection->first();
        $key = $collection->indexOf($entry);
        $form = $this->collectionContainer->getForm($key);
        $this->assertInstanceOf(CoreForm::class, $form);
        $this->assertSame($entry, $form->getObject());
        $this->assertContains($key, $form->getAttribute('action'));
    }
    
    /**
     * @depends testSetEntity
     */
    public function testGetFormNew(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $form = $this->collectionContainer->getForm(CollectionContainer::NEW_ENTRY);
        $this->assertInstanceOf(CoreForm::class, $form);
        $this->assertSame($this->newEntry, $form->getObject());
        $this->assertContains(CollectionContainer::NEW_ENTRY, $form->getAttribute('action'));
    }
    
    /**
     * @depends testSetEntity
     */
    public function testExecuteActionNonExistentName(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $this->assertSame([], $this->collectionContainer->executeAction('non-existent'));
    }
    
    /**
     * @depends testSetEntity
     */
    public function testExecuteActionExistentNameWithExistentEntry(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $entry = $collection->first();
        $count = count($collection);
        $result = $this->collectionContainer->executeAction('remove', ['key' => $collection->indexOf($entry)]);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertSame($count - 1, count($collection));
    }
    
    /**
     * @depends testSetEntity
     */
    public function testExecuteActionExistentNameWithNonExistentEntry(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $count = count($collection);
        $result = $this->collectionContainer->executeAction('remove', ['key' => 'non-existent']);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertSame($count, count($collection));
    }
    
    public function testGetViewHelper()
    {
        $result = $this->collectionContainer->getViewHelper();
        $this->assertTrue(is_string($result));
        $this->assertNotEmpty($result);
    }
    
    public function testSetViewHelper()
    {
        $expected = 'viewHelper';
        $this->assertSame($this->collectionContainer, $this->collectionContainer->setViewHelper($expected));
        $this->assertSame($expected, $this->collectionContainer->getViewHelper());
    }
    
    /**
     * @depends testSetEntity
     */
    public function getTemplateForm(Collection $collection)
    {
        $this->collectionContainer->setEntity($collection);
        $form = $this->collectionContainer->getTemplateForm();
        $this->assertInstanceOf(CoreForm::class, $form);
        $this->assertNull($form->getObject());
        $this->assertContains(CollectionContainer::NEW_ENTRY, $form->getAttribute('action'));
    }
    
    public function expectRuntimeExceptionWithEntityData()
    {
        return [
            ['getIterator'],
            ['count'],
            ['getForm', ['name']],
            ['executeAction', ['remove', ['key' => 'id']]]
        ];
    }
    
    public function expectRuntimeExceptionWithFormData()
    {
        return [
            ['getIterator', []],
            ['getForm', ['first']],
            ['getTemplateForm', []]
        ];
    }
}
