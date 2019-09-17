<?php

namespace CoreTest\Filter\File;

use PHPUnit\Framework\TestCase;

use Core\Filter\File\Entity;
use Core\Filter\File\EntityFactory;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;

/**
 * Class EntityFactoryTest
 * @package CoreTest\Filter\File
 * @author Anthonius Munthi <me@itstoni.com>
 */
class EntityFactoryTest extends TestCase
{
    public function testInvokation()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $target = new EntityFactory();
        $service = $target($container, 'someName', ['repository'=>false]);
        $this->assertInstanceOf(Entity::class, $service);
        $this->assertNull($service->getRepository());
    }

    public function testInvokeUsingGlobalRepositoriesOptions()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock()
        ;
        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $container->expects($this->once())
            ->method('get')
            ->with('repositories')
            ->willReturn($repositories)
        ;

        $target = new EntityFactory();
        $service = $target($container, 'someName', ['repository'=>true]);
        $this->assertEquals($repositories, $service->getRepository());
    }

    public function testInvokeUsingRepositoryNameOptions()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock()
        ;
        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $container->expects($this->once())
            ->method('get')
            ->with('repositories')
            ->willReturn($repositories)
        ;

        $repositories->expects($this->once())
            ->method('get')
            ->with('Some\\Class\\Name')
            ->willReturn($repositories)
        ;
        $target = new EntityFactory();
        $service = $target($container, 'someName', ['repository'=>'Some\\Class\\Name']);
        $this->assertEquals($repositories, $service->getRepository());
    }
}
