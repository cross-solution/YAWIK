<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
namespace CoreTest\Entity;

use Core\Entity\AttachableEntityTrait;
use Core\Entity\AttachableEntityInterface;
use Core\Entity\AttachableEntityManager;
use Core\Entity\IdentifiableEntityInterface;

/**
 * @coversDefaultClass \Core\Entity\AttachableEntityTrait
 */
class AttachableEntityTraitTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var AttachableEntityInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachableEntityTrait;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->attachableEntityTrait = $this->getObjectForTrait(AttachableEntityTrait::class);
    }
    
    /**
     * @covers ::addAttachedEntity()
     * @covers ::getAttachableEntityManager()
     * @expectedException \LogicException
     * @expectedExceptionMessage No attachable entity manager is set
     */
    public function testAddAttachedEntityWithoutManagerSet()
    {
        $entity = $this->getMockBuilder(IdentifiableEntityInterface::class)
            ->getMock();
        
        $this->attachableEntityTrait->addAttachedEntity($entity);
    }
    
    /**
     * @covers ::removeAttachedEntity()
     * @covers ::getAttachableEntityManager()
     * @expectedException \LogicException
     * @expectedExceptionMessage No attachable entity manager is set
     */
    public function testRemoveAttachedEntityWithoutManagerSet()
    {
        $this->attachableEntityTrait->removeAttachedEntity('someKey');
    }
    
    /**
     * @covers ::getAttachedEntity()
     * @covers ::getAttachableEntityManager()
     * @expectedException \LogicException
     * @expectedExceptionMessage No attachable entity manager is set
     */
    public function testGetAttachedEntityWithoutManagerSet()
    {
        $this->attachableEntityTrait->getAttachedEntity('someKey');
    }
    
    /**
     * @covers ::hasAttachedEntity()
     * @covers ::getAttachableEntityManager()
     * @expectedException \LogicException
     * @expectedExceptionMessage No attachable entity manager is set
     */
    public function testHasAttachedEntityWithoutManagerSet()
    {
        $this->attachableEntityTrait->hasAttachedEntity('someKey');
    }
    
    /**
     * @covers ::setAttachableEntityManager()
     */
    public function testSetAttachableEntityManagerWithoutManagerSet()
    {
        $attachableEntityManager = $this->getMockBuilder(AttachableEntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attachableEntityManager->expects($this->once())
            ->method('setReferences')
            ->with($this->callback(function ($references) {
                return is_array($references);
            }))
            ->willReturnSelf();
        
        $this->attachableEntityTrait->setAttachableEntityManager($attachableEntityManager);
    }
    
    /**
     * @covers ::addAttachedEntity()
     * @covers ::getAttachableEntityManager()
     */
    public function testAddAttachedEntityCallsManager()
    {
        $entity = $this->getMockBuilder(IdentifiableEntityInterface::class)
            ->getMock();
        $key = 'someKey';
        $attachableEntityManager = $this->injectManager($this->attachableEntityTrait);
        $attachableEntityManager->expects($this->once())
            ->method('addAttachedEntity')
            ->with($this->identicalTo($entity), $this->equalTo($key));
        $this->assertSame($this->attachableEntityTrait, $this->attachableEntityTrait->addAttachedEntity($entity, $key));
    }
    
    /**
     * @covers ::removeAttachedEntity()
     * @covers ::getAttachableEntityManager()
     */
    public function testRemoveAttachedEntityCallsManager()
    {
        $key = 'someKey';
        $return = new \stdClass();
        $attachableEntityManager = $this->injectManager($this->attachableEntityTrait);
        $attachableEntityManager->expects($this->once())
            ->method('removeAttachedEntity')
            ->with($this->equalTo($key))
            ->willReturn($return);
        $this->assertSame($return, $this->attachableEntityTrait->removeAttachedEntity($key));
    }
    
    /**
     * @covers ::getAttachedEntity()
     * @covers ::getAttachableEntityManager()
     */
    public function testGetAttachedEntityOmittingKey()
    {
        $attachableEntityManager = $this->injectManager($this->attachableEntityTrait);
        $attachableEntityManager->expects($this->never())
            ->method('getAttachedEntity');
        $this->assertNull($this->attachableEntityTrait->getAttachedEntity());
    }
    
    /**
     * @covers ::getAttachedEntity()
     * @covers ::getAttachableEntityManager()
     */
    public function testGetAttachedEntityCallsManager()
    {
        $key = 'someKey';
        $return = new \stdClass();
        $attachableEntityManager = $this->injectManager($this->attachableEntityTrait);
        $attachableEntityManager->expects($this->once())
            ->method('getAttachedEntity')
            ->with($this->equalTo($key))
            ->willReturn($return);
        $this->assertSame($return, $this->attachableEntityTrait->getAttachedEntity($key));
    }

    /**
     * @covers ::createAttachedEntity()
     */
    public function testCreateAttachedEntity()
    {
        $attachableEntityManager = $this->injectManager($this->attachableEntityTrait);
        $attachableEntityManager->expects($this->exactly(4))->method('createAttachedEntity')
            ->withConsecutive(
                ['EntityClass', [], null],
                ['EntityClass', 'testkey'],
                ['EntityClass', ['param' => 'value']],
                ['EntityClass', ['param' => 'value'], 'testkey']
            );

        $this->attachableEntityTrait->createAttachedEntity('EntityClass');
        $this->attachableEntityTrait->createAttachedEntity('EntityClass', 'testkey');
        $this->attachableEntityTrait->createAttachedEntity('EntityClass', ['param' => 'value']);
        $this->attachableEntityTrait->createAttachedEntity('EntityClass', ['param' => 'value'], 'testkey');
    }

    /**
     * @covers ::hasAttachedEntity()
     * @covers ::getAttachableEntityManager()
     */
    public function testHasAttachedEntityCallsManager()
    {
        $key = 'someKey';
        $return = new \stdClass();
        $attachableEntityManager = $this->injectManager($this->attachableEntityTrait);
        $attachableEntityManager->expects($this->once())
            ->method('getAttachedEntity')
            ->with($this->equalTo($key))
            ->willReturn($return);
        $this->assertTrue($this->attachableEntityTrait->hasAttachedEntity($key));
    }
    
    /**
     * @param AttachableEntityInterface $attachableEntityTrait
     * @return AttachableEntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function injectManager($attachableEntityTrait)
    {
        $attachableEntityManager = $this->getMockBuilder(AttachableEntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attachableEntityManager->method('setReferences')
            ->willReturnSelf();
        
        $attachableEntityTrait->setAttachableEntityManager($attachableEntityManager);
        
        return $attachableEntityManager;
    }
}