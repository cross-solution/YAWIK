<?php

/**
 * YAWIK
 *
 * @copyright 2020 Cross Solution
 */

declare(strict_types=1);

namespace AuthTest\Form\Validator;

use Auth\Entity\AnonymousUser;
use Auth\Entity\User;
use PHPUnit\Framework\TestCase;
use Auth\Form\Validator\UniqueLoginName;
use Auth\Repository\User as UserRepository;
use Core\Exception\MissingDependencyException;

/**
 * Tests for \Auth\Form\Validator\UniqueLoginName
 *
 * @author Mathias Gelhausen
 * @covers \Auth\Form\Validator\UniqueLoginName
 * @group Auth
 * @group Auth.Form
 * @group Aith.Form.Validator
 */
class UniqueLoginNameTest extends TestCase
{

    public function testThrowsExceptionIfDependenciesAreMissing()
    {
        $this->expectException(MissingDependencyException::class);

        (new UniqueLoginName())->isValid('test');
    }

    public function testValidationFailsIfLoginNameNotUniqueAndNoCurrentUserIsset()
    {
        $value = 'test';
        $repo = $this->prophesize(UserRepository::class);
        $user = new AnonymousUser();

        $repo->findByLogin($value)->willReturn($user)->shouldBeCalled();

        $target = new UniqueLoginName();
        $target->setUserRepository($repo->reveal());

        static::assertFalse($target->isValid($value));
        static::assertEquals(
            [UniqueLoginName::NOT_UNIQUE => 'The login name "test" is already in use.'],
            $target->getMessages()
        );
    }

    public function testValidationFailsIfLoginNameNotUniqueAndCurrentUserIsset()
    {
        $value = 'test';
        $repo = $this->prophesize(UserRepository::class);
        $user = new AnonymousUser();
        $currentUser = new AnonymousUser();

        $user->setLogin('userOne');
        $currentUser->setLogin('userTwo');

        $repo->findByLogin($value)->willReturn($user)->shouldBeCalled();

        $target = new UniqueLoginName();
        $target->setUserRepository($repo->reveal());
        $target->setCurrentUser($currentUser);

        static::assertFalse($target->isValid($value));
        static::assertEquals(
            [UniqueLoginName::NOT_UNIQUE => 'The login name "test" is already in use.'],
            $target->getMessages()
        );
    }

    public function testValidationSucceedIfLoginNameIsUnique()
    {
        $value = 'test';
        $repo = $this->prophesize(UserRepository::class);

        $repo->findByLogin($value)->willReturn(null)->shouldBeCalled();

        $target = new UniqueLoginName();
        $target->setUserRepository($repo->reveal());

        static::assertTrue($target->isValid($value));
    }

    public function testValidationSucceedIfCurrentUserIsChangedUser()
    {
        $value = 'test';
        $repo = $this->prophesize(UserRepository::class);
        $user = new User();
        $user->setLogin('testtest');

        $repo->findByLogin($value)->willReturn($user)->shouldBeCalled();

        $target = new UniqueLoginName();
        $target->setUserRepository($repo->reveal());
        $target->setCurrentUser($user);

        static::assertTrue($target->isValid($value));
    }
}
