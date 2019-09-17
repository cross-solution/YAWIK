<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\Image;
use Core\Entity\ImageSet;
use Core\Entity\ImageSetInterface;
use Core\Entity\Permissions;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for \Core\Entity\ImageSet
 *
 * @covers \Core\Entity\ImageSet
 * @coversDefaultClass \Core\Entity\ImageSet
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 */
class ImageSetTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|ImageSet|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        ImageSet::class,
        '@testCallProxiesToGet' => ['mock' => ['get']],
        '@testCallProxiesToSet' => ['mock' => ['set']],
        '@testSetImagesWithoutPermissions' => ['mock' => ['clear' => 1, 'set', 'setPermissions' => 0]],
        '@testSetImagesWithPermissions' => ['mock' => ['clear' => 1, 'set' => 0, 'setPermissions']],
    ];

    private $inheritance = [ ImageSetInterface::class ];

    /**
     * @covers ::__construct
     */
    public function testConstructionCreatesMongoId()
    {
        $this->assertAttributeInternalType('string', 'id', $this->target);
        $this->assertAttributeNotEmpty('id', $this->target);
    }

    public function testCallThrowsExceptionIfUnknownMethodIsCalled()
    {
        $this->expectException('\BadMethodCallException');
        $this->expectExceptionMessage('Unknown method');

        $this->target->unknownMethod();
    }

    public function testCallThrowsExceptionIfSetMissingArguments()
    {
        $this->expectException('\BadMethodCallException');
        $this->expectExceptionMessage('Missing argument');

        $this->target->setSomeImage();
    }

    public function testCallProxiesToGet()
    {
        $this->target->expects($this->exactly(2))->method('get')
            ->withConsecutive(
                ['key', true],
                ['key', false]
            )->willReturn(true);

        $this->assertTrue($this->target->getKey());
        $this->assertTrue($this->target->getKey(false));
    }

    public function testCallProxiesToSet()
    {
        $image = new Image();
        $this->target->expects($this->once())->method('set')->with('key', $image)->willReturn(true);

        $this->assertTrue($this->target->setKey($image));
    }

    /**
     * @covers ::clear()
     */
    public function testClear()
    {
        $this->assertSame($this->target, $this->target->clear(), 'fluent interface broken.');
        $images = $this->getMockBuilder(ArrayCollection::class)->setMethods(['clear'])->getMock();
        $images->expects($this->once())->method('clear')->will($this->returnSelf());

        $this->target->setImagesCollection($images);
        $this->target->clear();
    }

    public function testSetImagesWithoutPermissions()
    {
        $image = new Image();
        $images = ['name' => $image];

        $this->target->expects($this->once())->method('set')->with('name', $image);

        $this->assertSame($this->target, $this->target->setImages($images));
    }

    public function testSetImagesWithPermissions()
    {
        $permissions = new Permissions();
        $this->target->expects($this->once())->method('setPermissions')->with($permissions);

        $this->target->setImages([], $permissions);
    }

    /**
     * @covers ::getImages()
     */
    public function testGetImages()
    {
        $this->target->get('key');

        $iterator = new \ArrayIterator();

        $images = $this->getMockBuilder(ArrayCollection::class)->setMethods(['getIterator'])->getMock();
        $images->expects($this->once())->method('getIterator')->willReturn($iterator);

        $this->target->setImagesCollection($images);

        $this->target->get(ImageSet::ORIGINAL);
    }


    public function testGetReturnsNullIfNoOriginalIsset()
    {
        $this->assertNull($this->target->get(ImageSet::ORIGINAL));
    }

    /**
     * @covers ::get()
     */
    public function testGetReturnsExpectedImages()
    {
        $image = new Image();
        $this->target->setImages([ImageSet::ORIGINAL => $image]);

        $this->assertSame($image, $this->target->get('key'));
        $this->assertNull($this->target->get('key', false));

        $this->target->setImages(['key' => $image]);

        $this->assertSame($image, $this->target->get('key'));
    }

    /**
     * @covers ::set()
     */
    public function testSetRemovesImage()
    {
        $image = new Image();
        $image->setKey('key');
        $images = $this->getMockBuilder(ArrayCollection::class)->setMethods(['removeElement'])->getMock();
        $images->expects($this->once())->method('removeElement')->with($image);
        $images->add($image);

        $this->target->setImagesCollection($images);

        $this->target->set('key', new Image());
    }

    /**
     * @covers ::set()
     */
    public function testSetAddsImage()
    {
        $image = $this->getMockBuilder(Image::class)->setMethods(['setBelongsTo', 'setKey'])->getMock();
        $image->expects($this->once())->method('setBelongsTo')->with($this->isType('string'));
        $image->expects($this->once())->method('setKey')->with('key');

        $images = $this->getMockBuilder(ArrayCollection::class)->setMethods(['add'])->getMock();
        $images->expects($this->once())->method('add')->with($image);

        $this->target->setImagesCollection($images);

        $this->assertSame($this->target, $this->target->set('key', $image), 'Fluent interface broken');
    }

    /**
     * @covers ::setPermissions()
     */
    public function testSetPermissions()
    {
        $permissions = new Permissions();

        $image = new Image();
        $imagePermissions = $this->getMockBuilder(Permissions::class)->setMethods(['clear', 'inherit'])->getMock();
        $imagePermissions->expects($this->once())->method('clear');
        $imagePermissions->expects($this->once())->method('inherit')->with($permissions);

        $image->setPermissions($imagePermissions);

        $this->target->setImages(['key' => $image]);

        $this->assertSame($this->target, $this->target->setPermissions($permissions));
    }
}
