<?php

namespace Settings\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\MvcEvent;
use Settings\Entity\Settings as SettingsEntity;
use Core\Entity\EntityInterface;

class Settings 
{
    
    protected $userRepository;
    protected $settingsByUser;
    
    public function __construct() 
    {
        $this->settingsByUser = array();
        return $this;
    }
    
    
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function getUserRepository()
    {
        return $this->userRepository;
    }
    
    public function getSettingsByUser($user)
    {
        if ($user instanceOf EntityInterface) {
            $user = $user->getId();
        }
        if (isset($this->settingsByUser[$user])) {
            return $this->settingsByUser[$user];
        }
        $UserRepository = $this->getUserRepository();
        $userEntity = $UserRepository->find($user);
        
        if (isset($userEntity)) {
            $settingsData = $userEntity->getSettings();
            $this->settingsByUser[$user] = new SettingsEntity($this);
            $this->settingsByUser[$user]->setData($settingsData)->spawnAsEntities(); 
            return $this->settingsByUser[$user];
        }
        return Null;
    }
    
    public function onPostDispatch(MvcEvent $e) 
    {
        //throw new \Exception("Test");
        $UserRepository = $this->getUserRepository();
        foreach ($this->settingsByUser as $user => $settings) {
            if (!empty($user) && $settings->hasChanged()) {
                $userEntity = $UserRepository->find($user);
                $a = $settings->toArray();
                $userEntity->setSettings($a);
                $id = $UserRepository->save($userEntity);
            }
        }
    }
    
}