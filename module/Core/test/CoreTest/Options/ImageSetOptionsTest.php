<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace CoreTest\Options;

use PHPUnit\Framework\TestCase;

use Core\Entity\Image;
use Core\Entity\ImageSetInterface;
use Core\Options\ImageSetOptions;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Laminas\Stdlib\AbstractOptions;

/**
 * Tests for \Core\Options\ImageSetOptions
 *
 * @covers \Core\Options\ImageSetOptions
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class ImageSetOptionsTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = ImageSetOptions::class;

    private $inheritance = [ AbstractOptions::class ];

    private $properties = [
        ['entityClass', [
            'default' => Image::class,
            'setter_args' => Image::class
        ]],
        ['formElementName', ['default' => ImageSetInterface::ORIGINAL, 'value' => 'formElementNameValue']],
        ['images', ['default'=>[ImageSetInterface::THUMBNAIL => [100,100]], 'value' => ['a' => [1,1]]]],
    ];
}
