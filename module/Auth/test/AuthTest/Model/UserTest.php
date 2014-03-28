<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */


namespace AuthTest\Model;

use Auth\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    
    public function testUserModelImplementsUserInterface()
    {
        $user = new User();
        $this->assertInstanceOf('\Auth\Model\UserInterface', $user);
    }
    
    public function setterAndGetterTestProvider()
    {
        // passin 
        return array(
            array('email', 'test@mail'),
            array('firstname', 'test'),
            array('lastname', 'user'),
            array('displayname', 'Test User'),
            array('profile', array('identifier' => 'testUser'))
        );
    }

    /** @dataProvider setterAndGetterTestProvider */
    public function testSettingAndGettingProperties($method, $value)
    {
        $user = new User();
        
        $setMethod = "set$method";
        $getMethod = "get$method";
        
        $this->assertEmpty($user->$getMethod());
        $this->assertSame($user, $user->$setMethod($value));
        $this->assertEquals($value, $user->$getMethod());
    }
    
    public function testGetDisplayNameConcatsFirstAndLastNameIfNotSet()
    {
        $user = new User();
        
        $user->setFirstName('test');
        $user->setLastName('user');
        
        $this->assertEquals('test user', $user->getDisplayName());
    }
}