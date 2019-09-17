<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Hydrator;

use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\ImageSetHydrator;
use Core\Entity\Image;
use Core\Entity\ImageSet;
use Core\Entity\ImageSetInterface;
use Core\Options\ImageSetOptions;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\MongoDB\GridFSFile;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * Tests for \Core\Entity\Hydrator\ImageSetHydrator
 *
 * @covers \Core\Entity\Hydrator\ImageSetHydrator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Hydrator
 */
class ImageSetHydratorTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|ImageSetHydrator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        ImageSetHydrator::class,
        'getConstructorArgs',
        '@testConstruction' => false,
        '@testInheritance' => ['as_reflection' => true],
    ];

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $imagineMock;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $optionsMock;

    private $inheritance = [ HydratorInterface::class ];

    private function getConstructorArgs()
    {
        $this->imagineMock = $this->getMockBuilder(ImagineInterface::class)->getMockForAbstractClass();
        $this->optionsMock = $this->getMockBuilder(ImageSetOptions::class)
            ->setMethods(['getEntity', 'getImages', 'getFormElementName'])
            ->getMock();

        return [$this->imagineMock, $this->optionsMock];
    }

    public function testConstruction()
    {
        $this->getConstructorArgs();

        $target = new ImageSetHydrator($this->imagineMock, $this->optionsMock);

        $this->assertAttributeSame($this->imagineMock, 'imagine', $target);
        $this->assertAttributeSame($this->optionsMock, 'options', $target);
    }

    public function testExtractReturnsEmptyArray()
    {
        $this->assertEquals([], $this->target->extract('notCorrectObject'));

        $this->assertEquals([], $this->target->extract(new ImageSet()));
    }

    public function testExtract()
    {
        $this->optionsMock->expects($this->once())->method('getFormElementName')->willReturn('name');

        $set = new ImageSet();
        $img = $this->getMockBuilder(Image::class)->setMethods(['getId'])->getMock();
        $img->expects($this->once())->method('getId')->willReturn('imageId');
        $set->set(ImageSetInterface::ORIGINAL, $img);

        $this->assertEquals(['name' => 'imageId'], $this->target->extract($set));
    }

    public function testHydrationDoesNothingIfNoFileOrUploadError()
    {
        $this->imagineMock->expects($this->never())->method('open');

        $this->target->hydrate([], new ImageSet());

        $this->target->hydrate(['original' => ['error' => UPLOAD_ERR_NO_FILE]], new ImageSet());
    }

    public function testHydrate()
    {
        $data = [
            'original' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'testimage.png',
                'tmp_name' => 'tmpname',
                'type' => 'image/png',
            ],
        ];

        $imageSpecs = [
            ImageSetInterface::THUMBNAIL => [100,100],
            'large' => [10000,10000],
            'small' => [600,600],
        ];

        $entityMock = $this->getMockBuilder(Image::class)->setMethods(['setFile', 'setName', 'setType'])->getMock();
        $entityMock->expects($this->exactly(3))
            ->method('setFile')
            ->withConsecutive(
                ['tmpname'],
                [$this->isInstanceOf(GridFSFile::class)]
            )
            ->will($this->returnSelf());

        $entityMock->expects($this->exactly(3))
            ->method('setName')
            ->withConsecutive(
                ['testimage.png'],
                ['thumbnail-testimage.png'],
                ['small-testimage.png']
            )
            ->will($this->returnSelf());
        $entityMock->expects($this->exactly(3))->method('setType')->with('image/png')->will($this->returnSelf());


        $this->optionsMock->expects($this->once())->method('getImages')->willReturn($imageSpecs);
        $this->optionsMock->expects($this->any())->method('getEntity')->willReturn($entityMock);

        $image = $this->getMockBuilder(\Imagine\Image\ImageInterface::class)->setMethods(['thumbnail', 'getSize', 'resize', 'get'])->getMockForAbstractClass();
        $image->expects($this->once())->method('thumbnail')->with($this->equalTo(new Box(100, 100)), \Imagine\Image\ImageInterface::THUMBNAIL_INSET)->will($this->returnSelf());
        $image->expects($this->exactly(2))->method('getSize')->willReturn(new Box(700, 900));
        $image->expects($this->exactly(1))->method('resize')->will($this->returnSelf());
        $image->expects($this->exactly(2))->method('get')->willReturn('filecontentbytes');
        $this->imagineMock->expects($this->once())->method('open')->with('tmpname')->willReturn($image);

        $set = $this->getMockBuilder(ImageSet::class)->setMethods(['setImages'])->getMock();
        $set->expects($this->once())->method('setImages')->with(
            ['original' => $entityMock, 'thumbnail' => $entityMock, 'small' => $entityMock]
        )->will($this->returnSelf());

        $actual = $this->target->hydrate($data, $set);

        $this->assertSame($set, $actual);
    }
}
