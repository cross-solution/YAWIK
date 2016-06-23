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
use CoreTestUtils\TestCase\InitValueTrait;
use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;
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
    use InitValueTrait, SimpleSetterAndGetterTrait;

    public function getTestInitValue()
    {
        $ob = new Cv();
        return [
            [$ob, 'educations', new ArrayCollection()],
            [$ob, 'employments', new ArrayCollection()],
            [$ob, 'skills', new ArrayCollection()],
            [$ob, 'languageSkills', new ArrayCollection()],
            [$ob, 'nativeLanguages', array()],
            [$ob, 'preferredJob', new PreferredJob()]
        ];
    }

    public function getSetterAndGetterDataProvider()
    {
        $ob = new Cv();
        return [
            [$ob, 'languageSkills', new ArrayCollection()],
            [$ob, 'user', new User()],
            [$ob, 'contact', new Contact()],
            [$ob, 'nativeLanguages', []],
            [$ob, 'preferredJob', new PreferredJob()]
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
