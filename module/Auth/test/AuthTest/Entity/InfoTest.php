<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace AuthTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\Info;
use Auth\Entity\UserImage;

/**
 * Tests for Info
 *
 * @covers \Auth\Entity\Info
 * @coversDefaultClass \Auth\Entity\Info
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class InfoTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Info
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Info();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Auth\Entity\InfoInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsJobInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Auth\Entity\InfoInterface', $this->target);
    }

    /**
     * @testdox Allows setting the day of birth of the user
     * @covers \Auth\Entity\Info::getBirthDay
     * @covers \Auth\Entity\Info::setBirthDay
     */
    public function testSetGetBirthDay()
    {
        $input = '10';
        $this->target->setBirthDay($input);
        $this->assertEquals($input, $this->target->getBirthDay());
    }

    /**
     * @testdox Allows setting the Month of birth of the user
     * @covers \Auth\Entity\Info::getBirthMonth
     * @covers \Auth\Entity\Info::setBirthMonth
     */
    public function testSetGetBirthMonth()
    {
        $input = '10';
        $this->target->setBirthMonth($input);
        $this->assertEquals($input, $this->target->getBirthMonth());
    }

    /**
     * @testdox Allows setting the year of birth of the user
     * @covers \Auth\Entity\Info::getBirthYear
     * @covers \Auth\Entity\Info::setBirthYear
     */
    public function testSetGetBirthYear()
    {
        $input = '1970';
        $this->target->setBirthYear($input);
        $this->assertEquals($input, $this->target->getBirthYear());
    }

    /**
     * @testdox Allows setting the street of the user
     * @covers \Auth\Entity\Info::getStreet
     * @covers \Auth\Entity\Info::setStreet
     */
    public function testSetGetStreet()
    {
        $input = 'Test Road 13';
        $this->target->setStreet($input);
        $this->assertEquals($input, $this->target->getStreet());
    }

    /**
     * @testdox Allows setting the year of birth of the user
     * @covers \Auth\Entity\Info::getEmail
     * @covers \Auth\Entity\Info::setEmail
     */
    public function testSetGetEmail()
    {
        $input = 'name@domain.de';
        $this->target->setEmail($input);
        $this->assertEquals($input, $this->target->getEmail());
    }

    /**
     * @testdox Allows setting the year of birth of the user
     * @covers \Auth\Entity\Info::getImage
     * @covers \Auth\Entity\Info::setImage
     */
    public function testSetGetImage()
    {
        $input = new UserImage();
        $output = new UserImage();
        $this->target->setImage($input);
        $this->assertEquals($output, $this->target->getImage());
        $input = null;
        $output = null;
        $this->target->setImage($input);
        $this->assertEquals($output, $this->target->getImage());
    }

    /**
     * @testdox Allows setting a phone number of the user
     * @covers \Auth\Entity\Info::getPhone
     * @covers \Auth\Entity\Info::setPhone
     */
    public function testSetGetPhone()
    {
        $phone = '+49 1795077451';
        $this->target->setPhone($phone);
        $this->assertEquals($phone, $this->target->getPhone());
    }

    /**
     * @testdox Allows to set the gender of the user
     * @covers \Auth\Entity\Info::getGender
     * @covers \Auth\Entity\Info::setGender
     */
    public function testSetGetGender()
    {
        $gender = 'm';
        $this->target->setGender($gender);
        $this->assertEquals($gender, $this->target->getGender());
    }

    /**
     * @testdox Allows setting the users firstName
     * @covers \Auth\Entity\Info::getFirstName
     * @covers \Auth\Entity\Info::setFirstName
     */
    public function testSetGetFirstName()
    {
        $input = 'Carsten ';
        $output = 'Carsten';
        $this->target->setFirstName($input);
        $this->assertEquals($output, $this->target->getFirstName());
    }

    /**
     * @testdox Allows setting the users postalcode
     * @covers \Auth\Entity\Info::getPostalCode
     * @covers \Auth\Entity\Info::setPostalCode
     */
    public function testSetGetPostalCode()
    {
        $postalCode = '60486 ';
        $this->target->setPostalCode($postalCode);
        $this->assertEquals($postalCode, $this->target->getPostalCode());
    }

    /**
     * @testdox Allows setting the users city
     * @covers \Auth\Entity\Info::getCity
     * @covers \Auth\Entity\Info::setCity
     */
    public function testSetGetCity()
    {
        $city = ' Frankfurt am Main';
        $this->target->setCity($city);
        $this->assertEquals($city, $this->target->getCity());
    }

    /**
     * @testdox Allows setting the users city
     * @covers \Auth\Entity\Info::isEmailVerified
     * @covers \Auth\Entity\Info::setEmailVerified
     */
    public function testSetGetEmailVerified()
    {
        $city = true;
        $this->target->setEmailVerified($city);
        $this->assertEquals(true, $this->target->isEmailVerified());
        $city = false;
        $this->target->setEmailVerified($city);
        $this->assertEquals(false, $this->target->isEmailVerified());
    }

    /**
     * @testdox Allows setting the users city
     * @covers \Auth\Entity\Info::getHouseNumber
     * @covers \Auth\Entity\Info::setHouseNumber
     */
    public function testSetGetHouseNumber()
    {
        $input = ' 2-4';
        $output = ' 2-4';
        $this->target->setHouseNumber($input);
        $this->assertEquals($output, $this->target->getHouseNumber());
    }

    /**
     * @testdox Allows setting the users country
     * @covers \Auth\Entity\Info::getCountry
     * @covers \Auth\Entity\Info::setCountry
     */
    public function testSetGetCountry()
    {
        $input = 'Deutschland';
        $output = 'Deutschland';
        $this->target->setCountry($input);
        $this->assertEquals($output, $this->target->getCountry());
    }

    /**
     * @testdox Allows setting the users city
     * @covers \Auth\Entity\Info::getLastName
     * @covers \Auth\Entity\Info::setLastName
     */
    public function testSetGetLastName()
    {
        $input = ' Bleek ';
        $output = 'Bleek';
        $this->target->setLastName($input);
        $this->assertEquals($output, $this->target->getLastName());
    }

    public function provideGetDisplayNameTestData()
    {
        return array(
            array(null,        null,      "name@example.de", "name@example.de"),
            array(" Carsten ", " Bleek ", "name@example.de", "Carsten Bleek"),
            array(null,        " Bleek ", "name@example.de", "Bleek"),
            array(" Carsten ", null,      "name@example.de", "name@example.de"),
        );
    }

    /**
     * @testdox Allows setting the users city
     * @covers \Auth\Entity\Info::getLastName
     * @covers \Auth\Entity\Info::setLastName
     * @dataProvider provideGetDisplayNameTestData
     */
    public function testGetDisplayName($firstname, $lastname, $email, $result)
    {
        $this->target->setFirstName($firstname);
        $this->target->setLastName($lastname);
        $this->target->setEmail($email);
        $this->assertEquals($result, $this->target->getDisplayName());
    }
}
