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
use Auth\Entity\Token;
use Auth\Entity\User;

/**
 * Tests for User
 *
 * @covers \Auth\Entity\User
 * @coversDefaultClass \Auth\Entity\User
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  User
 * @group  User.Entity
 */
class UserTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var User
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new User();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Auth\Entity\UserInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsJobInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Auth\Entity\UserInterface', $this->target);
    }

    /**
     * @testdox Allows to set the login name of the user
     * @covers \Auth\Entity\User::getLogin
     * @covers \Auth\Entity\User::setLogin
     */
    public function testSetGetLogin()
    {
        $input = 'demo';
        $this->target->setLogin($input);
        $this->assertEquals($input, $this->target->getLogin());
    }

    public function provideRoleTestData()
    {
        return array(
            array(null,                 User::ROLE_USER),
            array(User::ROLE_ADMIN,     User::ROLE_ADMIN),
            array(User::ROLE_RECRUITER, User::ROLE_RECRUITER),
            array(User::ROLE_USER,      User::ROLE_USER),
        );
    }
    /**
     * @testdox Allows to set the role name of a user
     * @covers \Auth\Entity\User::getRole
     * @covers \Auth\Entity\User::setRole
     * @dataProvider provideRoleTestData
     */
    public function testSetGetRole($role, $expectedRole)
    {
        $this->target->setRole($role);
        $this->assertEquals($expectedRole, $this->target->getRole());
    }

    public function provideInfoTestData()
    {
        return array(
            array(new Info(),     new Info()),
        );
    }
    /**
     * @testdox Allows to set the role name of a user
     * @covers \Auth\Entity\User::getInfo
     * @covers \Auth\Entity\User::setInfo
     * @dataProvider provideInfoTestData
     */
    public function testSetGetInfo($info, $expectedInfo)
    {
        $this->target->setInfo($info);
        $this->assertEquals($expectedInfo, $this->target->getInfo());
    }

    /**
     * @testdox Allows to mark a user as draft
     * @covers \Auth\Entity\User::isDraft
     * @covers \Auth\Entity\User::setIsDraft
     */
    public function testSetIsDraft()
    {
        $input = true;
        $this->target->setIsDraft($input);
        $this->assertEquals($input, $this->target->isDraft());
        $input = false;
        $this->target->setIsDraft($input);
        $this->assertEquals($input, $this->target->isDraft());
    }

    /**
     * Do setter and getter methods work correctly?
     *
     * @param string $setter Setter method name
     * @param string $getter getter method name
     * @param mixed $value Value to set and test the getter method with.
     *
     * @dataProvider provideSetterTestValues
     */
    public function testSettingValuesViaSetterMethods($setter, $getter, $value)
    {
        $target = $this->target;

        if (is_array($value)) {
            $setValue = $value[0];
            $getValue = $value[1];
        } else {
            $setValue = $getValue = $value;
        }

        if (null !== $setter) {
            $object = $target->$setter($setValue);
            $this->assertSame($target, $object, 'Fluent interface broken!');
        }

        $this->assertSame($target->$getter(), $getValue);
    }


    /**
     * Provides datasets for testSettingValuesViaSetterMethods.
     *
     * @return array
     */
    public function provideSetterTestValues()
    {
        return array(
            array('setEmail', 'getEmail', 'mail@email.com'),
            array(null, 'getEmail', null),
            array('setSecret', 'getSecret', '123secret'),
            array(null, 'getSecret', null),
        );
    }



    /**
     * @testdox Allows to set a Token of a user
     * @covers \Auth\Entity\User::getTokens
     * @covers \Auth\Entity\User::setTokens
     * @dataProvider provideTokenTestData
     *
     */
    public function testSetGetToken($token, $expectedToken)
    {
        $this->target->setTokens($token);
        $this->assertEquals($expectedToken, $this->target->getTokens());
    }

    public function provideTokenTestData()
    {
        return array(
            array(new Token(),     new Token()),
        );
    }
}
