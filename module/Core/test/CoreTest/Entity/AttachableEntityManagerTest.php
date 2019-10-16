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

use PHPUnit\Framework\TestCase;

use Core\Entity\AttachableEntityManager;
use Core\Repository\RepositoryService;
use Core\Repository\AbstractRepository as Repository;
use Core\Entity\IdentifiableEntityInterface;

/**
 * @coversDefaultClass \Core\Entity\AttachableEntityManager
 */
class AttachableEntityManagerTest extends TestCase
{
    
    /**
     * @var AttachableEntityManager
     */
    protected $attachableEntityManager;
    
    /**
     * @var RepositoryService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositories;
    
    /**
     * @var array
     */
    protected $references;

    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();
        $this->references = [];

        if ('testCreateAttachedEntity' == $this->getName(false)) {
            $this->attachableEntityManager = $this->getMockBuilder(AttachableEntityManager::class)
                ->setConstructorArgs([$this->repositories])->setMethods(['addAttachedEntity'])->getMock();
        } else {
            $this->attachableEntityManager = new AttachableEntityManager($this->repositories);
            $this->attachableEntityManager->setReferences($this->references);
        }
    }
    
    /**
     * @covers ::__construct()
     * @covers ::setReferences()
     */
    public function testSetReferencesReturnsItself()
    {
        $references = [];
        $this->assertSame($this->attachableEntityManager, $this->attachableEntityManager->setReferences($references));
    }
    
    /**
     * @covers ::addAttachedEntity()
     */
    public function testAddAttachedEntityReturnsItself()
    {
        $entity = $this->getEntity('someId');
        $this->assertSame($this->attachableEntityManager, $this->attachableEntityManager->addAttachedEntity($entity));
    }
    
    /**
     * @covers ::addAttachedEntity()
     */
    public function testAddAttachedEntityWithoutKeyUseFQCNAsKey()
    {
        $entity = $this->getEntity('someId');
        $key = get_class($entity);
        $this->attachableEntityManager->addAttachedEntity($entity);
        $this->assertArrayHasKey($key, $this->references);
    }
    
    /**
     * @covers ::addAttachedEntity()
     */
    public function testAddAttachedEntityWithKey()
    {
        $entity = $this->getEntity('someId');
        $key = 'someKey';
        $this->attachableEntityManager->addAttachedEntity($entity, $key);
        $this->assertArrayHasKey($key, $this->references);
    }
    
    /**
     * @covers ::addAttachedEntity()
     */
    public function testAddAttachedEntityWithoutIdStoreEntity()
    {
        $entity = $this->getEntity(null);
        $className = get_class($entity);
        
        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('store')
            ->with($this->identicalTo($entity));
        
        $this->repositories->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo($className))
            ->willReturn($repository);
        
        $this->attachableEntityManager->addAttachedEntity($entity);
    }
    
    /**
     * @covers ::addAttachedEntity()
     */
    public function testAddAttachedEntityWithIdDoesNotStoreEntity()
    {
        $entity = $this->getEntity('someId');
        
        $this->repositories->expects($this->never())
            ->method('getRepository');
        
        $this->attachableEntityManager->addAttachedEntity($entity);
    }
    
    /**
     * @covers ::getAttachedEntity()
     */
    public function testGetAttachedEntityWithNonExistentKey()
    {
        $this->repositories->expects($this->never())
            ->method('getRepository');
        
        $this->assertNull($this->attachableEntityManager->getAttachedEntity('nonExistent'));
    }
    
    /**
     * @covers ::getAttachedEntity()
     */
    public function testGetAttachedEntityWithExistentKeyButNotExistingEntityInRepository()
    {
        $id = 'someId';
        $entity = $this->getEntity($id);
        $key = 'someKey';
        $className = get_class($entity);
        
        $this->attachableEntityManager->addAttachedEntity($entity, $key);
        $this->assertArrayHasKey($key, $this->references);
        
        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id));
        
        $this->repositories->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo($className))
            ->willReturn($repository);
        
        $this->assertNull($this->attachableEntityManager->getAttachedEntity($key));
        $this->assertArrayNotHasKey($key, $this->references);
    }
    
    /**
     * @covers ::getAttachedEntity()
     */
    public function testGetAttachedEntityWithExistentKeyWithExistingEntityInRepository()
    {
        $id = 'someId';
        $entity = $this->getEntity($id);
        $key = 'someKey';
        $className = get_class($entity);
        
        $this->attachableEntityManager->addAttachedEntity($entity, $key);
        $this->assertArrayHasKey($key, $this->references);
        
        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id))
            ->willReturn($entity);
        
        $this->repositories->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo($className))
            ->willReturn($repository);
        
        $this->assertSame($entity, $this->attachableEntityManager->getAttachedEntity($key));
        $this->assertArrayHasKey($key, $this->references);
    }

    public function provideTestCreateAttachedEntityData()
    {
        return [
            ['EntityClass' ],
            ['EntityClass', 'testkey' ],
            ['EntityClass', ['param' => 'value']],
            ['EntityClass', ['param' => 'value'], 'testkey'],
        ];
    }
    /**
     * @dataProvider provideTestCreateAttachedEntityData
     * @covers ::createAttachedEntity()
     */
    public function testCreateAttachedEntity($entityClass, $values = [], $key = null)
    {
        if (is_string($values)) {
            $expectKey = $values;
            $expectValues = [];
        } else {
            $expectKey = $key;
            $expectValues = $values;
        }

        $entity = $this->getEntity('testCreate');
        $repository = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();
        $repository->expects($this->once())->method('create')->with($expectValues)->willReturn($entity);
        $this->repositories->expects($this->once())->method('getRepository')->with($entityClass)->willReturn($repository);

        $this->attachableEntityManager->expects($this->once())->method('addAttachedEntity')
            ->with($entity, $expectKey);

        $this->attachableEntityManager->createAttachedEntity($entityClass, $values, $key);
    }

    /**
     * @covers ::removeAttachedEntity()
     */
    public function testRemoveAttachedEntityWithNonExistentKey()
    {
        $this->assertFalse($this->attachableEntityManager->removeAttachedEntity('nonExistent'));
    }
    
    /**
     * @covers ::removeAttachedEntity()
     */
    public function testRemoveAttachedEntityWithExistentKey()
    {
        $key = 'someKey';
        $this->references[$key] = 'someValue';
        $this->assertTrue($this->attachableEntityManager->removeAttachedEntity($key));
        $this->assertArrayNotHasKey($key, $this->references);
    }
    
    /**
     * @param string|null $id
     */
    protected function getEntity($id)
    {
        $entity = $this->getMockBuilder(IdentifiableEntityInterface::class)
            ->getMock();
        $entity->method('getId')
            ->willReturn($id);
        
        return $entity;
    }
}
