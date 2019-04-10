<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationTest\Factory\Form;

use PHPUnit\Framework\TestCase;

use Applications\Factory\Form\ContactImageFactory;

/**
 * @covers \Applications\Factory\Form\ContactImageFactory
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Factory
 * @group Applications.Factory.Form
 */
class ContactImageFactoryTest extends TestCase
{

    /**
     * @testdox Extends Auth\Form\UserImageFactory
     */
    public function testExtendsAuthFormUserImageFactory()
    {
        $baseClass = '\Auth\Form\UserImageFactory';
        $factory   = new ContactImageFactory();

        $this->assertInstanceOf($baseClass, $factory);
    }

    public function overridesPropertyProvider()
    {
        return array(
            array('fileEntityClass', '\Applications\Entity\Attachment'),
            array('configKey', 'application_contact_image'),
        );
    }

    /**
     *
     * @dataProvider overridesPropertyProvider
     *
     * @param $property
     * @param $value
     */
    public function testOverridesProperty($property, $value)
    {
        $factory = new ContactImageFactory();

        $this->assertObjectHasAttribute($property, $factory);
        $this->assertAttributeEquals($value, $property, $factory);
    }
}
