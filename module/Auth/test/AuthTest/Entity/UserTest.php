<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace AuthTest\Entity;

use Auth\Entity\Info;
use Auth\Entity\User;

/**
 * Tests for User
 *
 * @covers \Auth\Entity\User
 * @coversDefaultClass \Auth\Entity\User
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var User
     */
    private $target;

    public function setup()
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
     * @covers Auth\Entity\User::getLogin
     * @covers Auth\Entity\User::setLogin
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
     * @covers Auth\Entity\User::getRole
     * @covers Auth\Entity\User::setRole
     * @dataProvider provideRoleTestData
     */
    public function testSetGetRole($role,$expectedRole)
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
     * @covers Auth\Entity\User::getInfo
     * @covers Auth\Entity\User::setInfo
     * @dataProvider provideInfoTestData
     */
    public function testSetGetInfo($info,$expectedInfo)
    {
        $this->target->setInfo($info);
        $this->assertEquals($expectedInfo, $this->target->getInfo());
    }

    /**
     * @testdox Allows to set the secret of a user
     * @covers Auth\Entity\User::getSecret
     * @covers Auth\Entity\User::setSecret
     */
    public function testSetGetSecret()
    {
        $input = 'secret';
        $this->target->setSecret($input);
        $this->assertEquals($input, $this->target->getSecret());
    }

    /**
     * @testdox Allows to mark a user as draft
     * @covers Auth\Entity\User::isDraft
     * @covers Auth\Entity\User::setIsDraft
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

    public function provideEmailTestData()
    {
        return array(
            array(null,                "info1@example.com",  "info1@example.com"),
            array("user@example.com",  "info2@example.com",  "user@example.com"),
        );
    }
    /**
     * @testdox Allows to set the role name of a user
     * @covers Auth\Entity\User::getEmail
     * @covers Auth\Entity\User::setEmail
     * @dataProvider provideEmailTestData
     */
    public function testSetGetEmail($userEmail,$infoEmail,$expectedEmail)
    {
        $info = new Info();
        $info->setEmail($infoEmail);
        $this->target->setInfo($info);
        $this->target->setEmail($userEmail);
        $this->assertEquals($expectedEmail, $this->target->getEmail());
    }


}