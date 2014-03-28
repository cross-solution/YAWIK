<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace AuthTest\Adapter;

require_once(__DIR__ . '/../../../../../vendor/hybridauth/hybridauth/hybridauth/Hybrid/User_Profile.php');
require_once(__DIR__ . '/../../../../../vendor/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model.php');

use \Auth\Adapter\HybridAuth as Adapter;

class HybridAuthTest extends \PHPUnit_Framework_TestCase
{

    public function testSettingAndGettingProviderIndentifier()
    {
       
        $adapter = new Adapter();
        $this->assertSame($adapter, $adapter->setProvider('test'));
        $this->assertEquals('test', $adapter->getProvider());
        
    }
    
    public function testSettingAndGettingHybridAuthInstance()
    {
        $adapter = new Adapter();
        $hauth = $this->getMock('\Hybrid_Auth', array(), array(), 'HybridAuthMock', false);
        
        $this->assertSame($adapter, $adapter->setHybridAuth($hauth));
        $this->assertSame($hauth, $adapter->getHybridAuth());
    }
    
    public function testSettingAndGettingMapper()
    {
        $adapter = new Adapter();
        $mapper = $this->getMock('\Auth\Mapper\UserMapperInterface');
        
        $this->assertSame($adapter, $adapter->setMapper($mapper));
        $this->assertSame($mapper, $adapter->getMapper());
    }
    
    private function getTestAdapter($hauthUserProfile, $userModel, $expectsSave = true)
    {
        $provider = 'test';
        
        
        
        $hauthAdapter = $this->getMockForAbstractClass('\Hybrid_Provider_Model', array(), 'HybridProviderModelMock', false, true, true, array('getUserProfile'));
        $hauthAdapter->expects($this->once())
            ->method('getUserProfile')
            ->will($this->returnValue($hauthUserProfile));
        
        
        $mapperMethods = null === $userModel
            ? array('findByProfileIdentifier', 'create', 'save')
            : array('findByProfileIdentifier', 'save');
        $mapper = $this->getMockForAbstractClass('\Auth\Mapper\UserMapperInterface', array('findByProfileIdentifier'));
        $mapper->expects($this->once())
            ->method('findByProfileIdentifier')
            ->will($this->returnValue($userModel));
        if ($expectsSave) {
        $mapper->expects($this->atLeastOnce())
            ->method('save');
        } else {
            $mapper->expects($this->never())
            ->method('save');
        }
            
        if (null === $userModel) {
            $mapper->expects($this->once())
                ->method('create')
                ->will($this->returnValue(new \Auth\Model\User()));    
        }
        
        $adapter = $this->getMock('\Auth\Adapter\HybridAuth', array('getHybridAuth'));
        $adapter->expects($this->any())
        ->method('getHybridAuth')
        ->will($this->returnValue(new HybridAuthMock($hauthAdapter)));
        
        $adapter->setProvider($provider);
        
        //$adapter->setHybridAuth($hybridAuth);
        $adapter->setMapper($mapper);
        
        return $adapter;
    }
    
    public function testAuthenticateWithNoVerifiedAddress()
    {
        
        $hauthUserProfile = new \Hybrid_User_Profile();
        $hauthUserProfile->identifier = 'testUser';
        $hauthUserProfile->email = 'testUser@testSystem';
        
        $userModel = new \Auth\Model\User();
        $userModel->setData(array(
            'id' => 'test',
            'firstName' => 'test',
            'lastName' => 'testUser',
            
        ));
        
        $adapter = $this->getTestAdapter($hauthUserProfile, $userModel);
        
        $result = $adapter->authenticate('test');
        $user = $result->getIdentity();
        $this->assertEquals($user->email, $hauthUserProfile->email);
        
        
    }
    
    public function testAuthenticateWithVerifiedAddress()
    {
        require_once(__DIR__ . '/../../../../../vendor/hybridauth/hybridauth/hybridauth/Hybrid/User_Profile.php');
        $hauthUserProfile = new \Hybrid_User_Profile();
        $hauthUserProfile->identifier = 'testUser';
        $hauthUserProfile->email = 'testUser@testSystem';
        $hauthUserProfile->emailVerified = 'verifiedTestUser@testSystem';
    
        $userModel = new \Auth\Model\User();
        $userModel->setData(array(
            'id' => 'test',
            'firstName' => 'test',
            'lastName' => 'testUser',
    
        ));
    
        $adapter = $this->getTestAdapter($hauthUserProfile, $userModel);
    
        $result = $adapter->authenticate('test');
        $user = $result->getIdentity();
        $this->assertEquals($hauthUserProfile->emailVerified, $user->email);
    
        

    }
    
    public function testUserIsCreatedWhenUnknown()
    {
        require_once(__DIR__ . '/../../../../../vendor/hybridauth/hybridauth/hybridauth/Hybrid/User_Profile.php');
        $hauthUserProfile = new \Hybrid_User_Profile();
        $hauthUserProfile->identifier = 'testUser';
        $hauthUserProfile->email = 'testUser@testSystem';
        $hauthUserProfile->emailVerified = 'verifiedTestUser@testSystem';
        
        $adapter = $this->getTestAdapter($hauthUserProfile, null);
        
        $result = $adapter->authenticate('test');
        $user = $result->getIdentity();
        
        $this->assertInstanceOf('\Auth\Model\UserInterface', $user);
    }
    
    public function testUserIsNotSavedIfNoUpdatedInfo()
    {
        require_once(__DIR__ . '/../../../../../vendor/hybridauth/hybridauth/hybridauth/Hybrid/User_Profile.php');
        $hauthUserProfile = new \Hybrid_User_Profile();
        $hauthUserProfile->identifier = 'testUser';
        $hauthUserProfile->email = 'testUser@testSystem';
        $hauthUserProfile->emailVerified = 'verifiedTestUser@testSystem';
        
       
        
        $userModel = new \Auth\Model\User();
        $userModel->setData(array(
            'id' => 'test',
            'firstName' => 'test',
            'lastName' => 'testUser',
            'email' => 'verifiedTestUser@testSystem',
            'profile' => (array) $hauthUserProfile,
    
        ));
                
        $adapter = $this->getTestAdapter($hauthUserProfile, $userModel, false);
        
        $result = $adapter->authenticate('test');
        
    }
}

class HybridAuthMock
{
    protected $_authReturn;

    public function __construct($authReturn)
    {
        $this->_authReturn = $authReturn;
    }

    public function authenticate($provider)
    {
        return $this->_authReturn;
    }
}





