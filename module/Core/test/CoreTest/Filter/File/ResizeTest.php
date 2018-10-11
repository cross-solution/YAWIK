<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Filter\File;

use Core\Filter\File\Resize;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Imagine\Gd\Imagine;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;

/**
 * Class ResizeTest
 *
 * @package CoreTest\Filter\File
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ResizeTest extends \PHPUnit_Framework_TestCase
{
    use TestSetterGetterTrait;

    /**
     * @var Resize
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $image;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $imagine;

    /**
     * @var string
     */
    private $imageFile;

    public function setUp()
    {
        $this->target = new Resize([]);
    }

    public function propertiesProvider()
    {
        $imagine = new Imagine();
        return [
            ['imagine', $imagine],
        ];
    }

    /**
     * @dataProvider getTestSkipFilter
     */
    public function testSkipFilter($value)
    {
        $imagine = $this->getMockBuilder(ImagineInterface::class)
            ->getMock()
        ;

        $target = $this->target;
        $target->setImagine($imagine);
        $imagine->expects($this->never())
            ->method('open')
        ;

        $target->filter($value);
    }

    public function getTestSkipFilter()
    {
        return [
            ['foo'],
            [['error' => UPLOAD_ERR_NO_FILE]],
            [
                ['type' => 'pdf','error' => UPLOAD_ERR_OK],
            ]
        ];
    }

    private function setupImagine()
    {
        $this->imageFile = __DIR__ . '/fixtures/logo.jpg';
        $image = $this->getMockBuilder(ImageInterface::class)
            ->getMock()
        ;
        $imagine = $this->getMockBuilder(ImagineInterface::class)
            ->getMock()
        ;
        $imagine->expects($this->once())
            ->method('open')
            ->with($this->imageFile)
            ->willReturn($image)
        ;

        $this->imagine = $imagine;
        $this->image = $image;
    }

    public function testFilterWithDefinedWidthAndHeightOptions()
    {
        $this->setupImagine();

        $target = $this->target;
        $target->setOptions([
            'width' => 100,
            'height' => 100
        ]);
        $target->setImagine($this->imagine);

        $value = [
            'error' => UPLOAD_ERR_OK,
            'tmp_name' => $this->imageFile,
            'type' => 'image/jpg',
        ];

        $this->image->expects($this->once())
            ->method('resize')
            ->with($this->callback(function ($size) {
                $this->assertInstanceOf(BoxInterface::class, $size);
                $this->assertEquals(100, $size->getWidth());
                $this->assertEquals(100, $size->getHeight());
                return true;
            }))
        ;
        $this->image->expects($this->once())
            ->method('save')
            ->with(
                $this->imageFile,
                ['format' => 'jpg']
            );

        $alreadyFiltered = $target->filter($value);
        $this->assertEquals(filesize($this->imageFile), $alreadyFiltered['size']);

        // testing filter doesn't process $value twice
        $target->filter($value);
    }

    /**
     * @param int $minMaxValue
     * @dataProvider getTestMinMaxOptions
     */
    public function testMinMaxOptions($options, $widen, $heighten)
    {
        $this->setupImagine();
        $size = $this->getMockBuilder(BoxInterface::class)
            ->getMock()
        ;
        $this->image->expects($this->once())
            ->method('getSize')
            ->willReturn($size)
        ;

        $size->expects($this->once())
            ->method('getWidth')
            ->willReturn(100)
        ;
        $size->expects($this->once())
            ->method('getHeight')
            ->willReturn(100)
        ;

        $size->expects($this->once())
            ->method('widen')
            ->with($widen)
            ->willReturn($size)
        ;
        $size->expects($this->once())
            ->method('heighten')
            ->with($heighten)
            ->willReturn($size)
        ;

        $target = $this->target;
        $target->setOptions($options);
        $target->setImagine($this->imagine);

        $value = [
            'error' => UPLOAD_ERR_OK,
            'tmp_name' => $this->imageFile,
            'type' => 'image/jpg',
        ];

        $target->filter($value);
    }


    public function getTestMinMaxOptions()
    {
        return [
            [ ['max-width'=>50, 'max-height'=> 50 ], 50, 50],
            [ ['max-width'=>80, 'max-height'=> 90 ], 80,90],
            [ ['min-width'=>200, 'min-height' => 120],200,120]
        ];
    }
}
