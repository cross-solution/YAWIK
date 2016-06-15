<?php

/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author fedys
 * @license   AGPLv3
 */

namespace CvTest\Entity;


use Auth\Entity\Info;
use Auth\Entity\User;
use Core\Collection\IdentityWrapper;
use Cv\Entity\Contact;
use Cv\Entity\Cv;
use Cv\Entity\PreferredJob;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CvTest
 * @package CvTest\Entity
 * @covers \Cv\Entity\Cv
 */
class CvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $propName
     * @param $expectedValue
     * @dataProvider getTestInitValue
     */
    public function testInitValue($propName, $expectedValue)
    {
        $cv = new Cv();
        if (is_object($expectedValue)) {
            $this->assertInstanceOf(
                get_class($expectedValue),
                $cv->$propName(),
                '::' . $propName . '() init value should return a type of ' . get_class($expectedValue)
            );
        } elseif (is_array($expectedValue)) {
            $this->assertSame(
                $expectedValue,
                $cv->$propName(),
                '::' . $propName . '() init value should return an empty array'
            );
        } else {
            $this->assertEquals(
                $expectedValue,
                $cv->$propName(),
                '::' . $propName . '() init value should return ' . $expectedValue
            );
        }
    }

    public function getTestInitValue()
    {
        return [
            ['getEducations', new ArrayCollection()],
            ['getEmployments', new ArrayCollection()],
            ['getSkills', new ArrayCollection()],
            ['getLanguageSkills', new ArrayCollection()],
            ['getNativeLanguages', array()],
            ['getPreferredJob', new PreferredJob()]
        ];
    }

    /**
     * @param $propertyName
     * @param $propertyValue
     * @dataProvider getTestSetAndGet
     */
    public function testSetAndGet($propertyName, $propertyValue)
    {
        $cv = new Cv();
        $setter = 'set' . $propertyName;
        $getter = 'get' . $propertyName;

        call_user_func(array($cv, $setter), $propertyValue);
        $this->assertSame(
            $propertyValue,
            call_user_func(array($cv, $getter)),
            '::' . $setter . '() and ::' . $getter . '() should executed properly'
        );
    }

    public function getTestSetAndGet()
    {
        return [
            ['languageSkills', new ArrayCollection()],
            ['user', new User()],
            ['contact', new Contact()],
            ['nativeLanguages', []],
            ['preferredJob', new PreferredJob()]
        ];
    }

    /**
     * @param $propName
     * @dataProvider getTestIndexedById
     */
    public function testGetIndexedById($propName)
    {
        $cv = new Cv();
        $col = new ArrayCollection();
        $setter = 'set' . $propName;
        $getter = 'get' . $propName . 'IndexedById';

        call_user_func(array($cv, $setter), $col);
        $this->assertInstanceOf(
            IdentityWrapper::class,
            call_user_func(array($cv, $getter)),
            '::' . $getter . '() should return an indexed by id value'
        );
    }

    public function getTestIndexedById()
    {
        return [
            ['languageSkills'],
            ['employments'],
            ['educations'],
            ['skills'],
        ];
    }

    public function testSetContact()
    {
        $cv = new Cv();
        $info = new Info();

        $cv->setContact($info);

        $this->assertInstanceOf(
            Contact::class,
            $cv->getContact(),
            '::setContact() should convert value into Contact class'
        );
    }

    public function testIsDraft()
    {
        $cv = new Cv();

        $cv->setIsDraft(true);
        $this->assertEquals(
            true,
            $cv->isDraft(),
            '::setIsDraft() and ::isDraft() should executed properly'
        );
    }
}
