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

use Auth\Entity\AnonymousUser;

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
class AnonymousUserTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var AnonymousUser
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new AnonymousUser();
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
     * @covers \Auth\Entity\AnonymousUser::getRole
     */
    public function testGetRole()
    {
        $this->assertEquals("guest", $this->target->getRole());
    }

    /**
     * @testdox Allows to set the login name of the user
     * @covers \Auth\Entity\AnonymousUser::setCredential
     */
    public function testSetCredential()
    {
        $input = 'demo';
        $this->target->setCredential($input);
        $this->assertEquals(null, $this->target->getCredential());
    }

    /**
     * @testdox Allows to set the login name of the user
     * @covers \Auth\Entity\AnonymousUser::setSecret
     */
    public function testSetSecret()
    {
        $input = 'demo';
        $this->target->setSecret($input);
        $this->assertEquals(null, $this->target->getSecret());
    }

    /**
     * @covers \Auth\Entity\AnonymousUser::getToken
     * @covers \Auth\Entity\AnonymousUser::getId
     */
    public function testGetId()
    {
        $this->assertEquals('token:' . $this->target->getToken(), $this->target->getId());

        $id=uniqid();
        $this->target->setId($id);
        $this->assertEquals($id, $this->target->getId());
    }

    /**
     * @covers \Auth\Entity\AnonymousUser::preventPersistence
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Anonymous users may not be persisted.
     */
    public function testPreventPersistance()
    {
        $this->target->preventPersistence();
    }
}
