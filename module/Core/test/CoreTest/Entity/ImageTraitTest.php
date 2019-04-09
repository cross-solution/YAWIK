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

use Core\Entity\ImageTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\ImageTrait
 *
 * @covers \Core\Entity\ImageTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class ImageTraitTest extends TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = Itt_ImageTrait::class;

    private $properties = [
        ['belongsTo', ['value' => 'imageSetId', 'getter_method' => '*']],
        ['key', 'key'],
    ];
}

class Itt_ImageTrait
{
    use ImageTrait;
}
