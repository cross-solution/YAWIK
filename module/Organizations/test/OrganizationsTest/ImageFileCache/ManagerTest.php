<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\ImageFileCache;

use PHPUnit\Framework\TestCase;

use Organizations\ImageFileCache\Manager;
use Organizations\Options\ImageFileCacheOptions as Options;
use Organizations\Entity\OrganizationImage as ImageEntity;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @coversDefaultClass \Organizations\ImageFileCache\Manager
 */
class ManagerTest extends TestCase
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->options = new Options();
        $this->manager = new Manager($this->options);
        
        $root = 'root';
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory($root));
        $this->options->setFilePath(vfsStream::url($root));
    }
    
    /**
     * @covers ::__construct
     * @covers ::getUri
     * @covers ::getImageSubPath
     */
    public function testGetUri()
    {
        $id = 'someId';
        $image = new ImageEntity();
        $image->setId($id);
        $image->setName('filename.ext');
        
        $this->options->setEnabled(false);
        $this->assertEquals($image->getUri(), $this->manager->getUri($image));
        
        $this->options->setEnabled(true);
        $cachedUri = $this->manager->getUri($image);
        $this->assertNotEquals($image->getUri(), $cachedUri);
        $this->assertContains($this->options->getUriPath(), $cachedUri);
    }
    
    /**
     * @covers ::isEnabled
     */
    public function testIsEnabled()
    {
        $this->options->setEnabled(false);
        $this->assertFalse($this->manager->isEnabled());
        
        $this->options->setEnabled(true);
        $this->assertTrue($this->manager->isEnabled());
    }
    
    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::getImageSubPath
     * @covers ::createDirectoryRecursively
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage image must have ID
     */
    public function testStoreWithImageWithoutId()
    {
        $image = new ImageEntity();
        
        $this->manager->store($image);
    }
    
    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::getImageSubPath
     * @covers ::createDirectoryRecursively
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage unable to get an image file extension
     */
    public function testStoreWithImageWithoutFileNameAndWithoutMimeType()
    {
        $image = new ImageEntity();
        $image->setId('someId');
        
        $this->manager->store($image);
    }
    
    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::getImageSubPath
     * @covers ::createDirectoryRecursively
     */
    public function testStoreWithImageWithoutFileNameButWithMimeType()
    {
        $image = new ImageEntity();
        $image->setId('someId');
        $image->setType('image/jpeg');
        
        $this->manager->store($image);
        
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild(sprintf('d/I/%s.%s', 'someId', 'jpeg')));
    }
    
    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::createDirectoryRecursively
     * @expectedException \RuntimeException
     * @expectedExceptionMessage unable to create directory
     */
    public function testStoreWithInsufficientPermissions()
    {
        $image = new ImageEntity();
        $image->setId('someId');
        $image->setName('filename.ext');
        
        vfsStreamWrapper::getRoot()->chmod(000);
        $this->manager->store($image);
    }
    
    /**
     * @covers ::store
     * @covers ::delete
     * @covers ::getImagePath
     * @covers ::createDirectoryRecursively
     */
    public function testStoreAndDelete()
    {
        $id = 'someId';
        $ext = 'ext';
        $name = 'filename.' . $ext;
        $path = sprintf('d/I/%s.%s', $id, $ext);
        $resource = 'someResource';
        $image = $this->getMockBuilder(ImageEntity::class)
            ->setMethods(['getResource'])
            ->getMock();
        $image->setId($id);
        $image->setName($name);
        $image->method('getResource')
            ->willReturn($resource);
        
        $this->manager->store($image);
        
        $root = vfsStreamWrapper::getRoot();
        $this->assertTrue($root->hasChild($path));
        $this->assertEquals($resource, $root->getChild($path)->getContent());
        
        $this->manager->delete($image);
        $this->assertFalse($root->hasChild($path));
    }
    
    /**
     * @param string $uri
     * @param string|null $expected
     * @covers ::matchUri
     * @dataProvider dataMatchUri
     */
    public function testMatchUri($uri, $expected)
    {
        $this->assertSame($expected, $this->manager->matchUri($this->options->getUriPath() . $uri));
    }
    
    /**
     * @return array
     */
    public function dataMatchUri()
    {
        return [
            ['/s/9/somename.jpg', 'somename'],
            ['/s/9/somename.jPg', 'somename'],
            ['/s/9/someName.jpg', null],
            ['/S/9/somename.jpg', null],
            ['/s/somename.jpg', null],
            ['/somename.jpg', null],
            ['somename.jpg', null],
            ['', null]
        ];
    }
}
