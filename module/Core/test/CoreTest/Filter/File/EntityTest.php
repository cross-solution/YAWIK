<?php

namespace CoreTest\Filter\File;

use Core\Entity\File;
use Core\Entity\FileInterface;
use Core\Entity\Image;
use PHPUnit\Framework\TestCase;

use Auth\Entity\UserInterface;
use Core\Entity\FileEntity;
use Core\Filter\File\Entity;
use Core\Repository\AbstractRepository;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Class EntityTest
 * @package CoreTest\Filter\File
 * @author Anthonius Munthi <me@itstoni.com>
 */
class EntityTest extends TestCase
{
    use TestSetterGetterTrait;

    /**
     * @var Entity
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new Entity([]);
    }

    public function testCreation()
    {
        // check file entity setting
        $file = new Image();
        $ob = new Entity($file);
        $this->assertEquals($file, $ob->getFileEntity());

        // check options setting
        $options = ['user' => 'some_user'];
        $ob = new Entity($options);
        $this->assertEquals('some_user', $ob->getUser());
    }

    public function propertiesProvider()
    {
        return [
            ['user', 'some_user'],
            ['repository', 'some_repository'],
        ];
    }

    public function testSetFileEntity()
    {
        $fe = new Image();
        $this->target->setFileEntity($fe);
        $this->assertInstanceOf(FileInterface::class, $this->target->getFileEntity());

        $this->target->setFileEntity(Image::class);
        $this->assertInstanceOf(FileInterface::class, $this->target->getFileEntity());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /No file entity set/
     */
    public function testGetFileEntityWhenEmpty()
    {
        $this->target->getFileEntity();
    }

    public function testFilterWithNonExistentFile()
    {
        $target = $this->target;
        $this->assertNull($target->filter(null));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File upload failed.
     */
    public function testFilterWhenError()
    {
        $target = $this->target;
        $value = [
            'tmp_name' => 'some_name',
            'error' => UPLOAD_ERR_CANT_WRITE
        ];
        $target->filter($value);
    }

    /*
     *
    FIXME: odm-module3 incompatible test
    public function testFilter()
    {
        $repository = $this->getMockBuilder(AbstractRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $user = $this->getMockBuilder(UserInterface::class)
            ->getMock()
        ;

        $fe = new Image();
        $target = $this->target;
        $target->setFileEntity($fe);
        $target->setRepository($repository);
        $target->setUser($user);

        $repository->expects($this->once())
            ->method('store')
            ->with($this->isInstanceOf(FileInterface::class))
        ;
        $value = [
            'name' => 'some_name',
            'type' => 'some_type',
            'tmp_name' => 'tmp_name',
            'error' => UPLOAD_ERR_OK,
        ];
        $value = $target->filter($value);
        $this->assertInstanceOf(FileInterface::class, $value['entity']);

        $alreadyFiltered = $target->filter($value);
        $this->assertSame($value, $alreadyFiltered);
    }
    */
}
