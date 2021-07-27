<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\ImageFileCache;

use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationImageMetadata;
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
        $image = $this->createMock(OrganizationImage::class);
        $image->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $image->expects($this->any())
            ->method('getName')
            ->willReturn('filename.ext');

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
     */
    public function testStoreWithImageWithoutId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('image must have ID');
        $image = $this->createMock(OrganizationImage::class);
        $image->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->manager->store($image, 'some contents');
    }

    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::getImageSubPath
     * @covers ::createDirectoryRecursively
     */
    public function testStoreWithImageWithoutFileNameAndWithoutMimeType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('unable to get an image file extension');
        $image = $this->createMock(OrganizationImage::class);
        $metadata = $this->getMockBuilder(OrganizationImageMetadata::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContentType'])
            ->getMock()
        ;

        $image->expects($this->once())
            ->method('getId')
            ->willReturn('id');
        $image->expects($this->once())
            ->method('getMetadata')
            ->willReturn($metadata);

        $metadata->expects($this->once())
            ->method('getContentType')
            ->willReturn(null);
        $this->manager->store($image, 'contents');
    }

    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::getImageSubPath
     * @covers ::createDirectoryRecursively
     */
    public function testStoreWithImageWithoutFileNameButWithMimeType()
    {
        $image = $this->createMock(OrganizationImage::class);
        $metadata = $this->getMockBuilder(OrganizationImageMetadata::class)
            ->setMethods(['getContentType'])
            ->getMock();

        $image->expects($this->once())
            ->method('getId')
            ->willReturn('someId');
        $image->expects($this->once())
            ->method('getMetadata')
            ->willReturn($metadata);
        $metadata->expects($this->once())
            ->method('getContentType')
            ->willReturn('image/jpeg');

        $this->manager->store($image, 'contents');

        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild(sprintf('d/I/%s.%s', 'someId', 'jpeg')));
    }

    /**
     * @covers ::store
     * @covers ::getImagePath
     * @covers ::createDirectoryRecursively
     */
    public function testStoreWithInsufficientPermissions()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('unable to create directory');
        $image = $this->createMock(OrganizationImage::class);
        $metadata = $this->getMockBuilder(OrganizationImageMetadata::class)
            ->setMethods(['getContentType'])
            ->getMock();

        $image->expects($this->once())
            ->method('getId')
            ->willReturn('someId');
        $image->expects($this->once())
            ->method('getMetadata')
            ->willReturn($metadata);
        $metadata->expects($this->once())
            ->method('getContentType')
            ->willReturn('image/jpeg');

        vfsStreamWrapper::getRoot()->chmod(000);
        $this->manager->store($image, 'contents');
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

        $image = $this->createMock(OrganizationImage::class);
        $metadata = $this->getMockBuilder(OrganizationImageMetadata::class)
            ->setMethods(['getContentType'])
            ->getMock();
        $image->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('someId');
        $image->expects($this->atLeastOnce())
            ->method('getMetadata')
            ->willReturn($metadata);
        $image->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);


        $this->manager->store($image, $resource);

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
