<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Factory\Form;

use PHPUnit\Framework\TestCase;

use Auth\Form\UserImageFactory;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Factory\Form\CvContactImageFactory;

/**
 * Tests for \Cv\Factory\Form\CvContactImageFactory
 *
 * @covers \Cv\Factory\Form\CvContactImageFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class CvContactImageFactoryTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    private $target = CvContactImageFactory::class;

    private $inheritance = [ UserImageFactory::class ];

    private $attributes = [
        'fileEntityClass' => 'Cv\Entity\ContactImage',
    ];
}
