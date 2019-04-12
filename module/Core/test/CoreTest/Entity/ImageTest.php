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

use Core\Entity\FileEntity;
use Core\Entity\Image;
use Core\Entity\ImageInterface;
use Core\Entity\ImageTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;

/**
 * Tests for \Core\Entity\Image
 *
 * @covers \Core\Entity\Image
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class ImageTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait;

    private $target = Image::class;

    private $inheritance = [ FileEntity::class, ImageInterface::class ];

    private $traits = [ ImageTrait::class ];
}
