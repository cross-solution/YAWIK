<?php

namespace Settings\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\MvcEvent;
use Settings\Entity\Settings as SettingsEntity;
use Core\Entity\EntityInterface;
use Core\Entity\EntityResolverStrategyInterface;
use Core\Repository\AbstractRepository;

class Settings extends AbstractRepository implements EntityResolverStrategyInterface
{
    
    protected $userRepository;
    protected $settingsByUser;
    protected $serviceLocator;
    
    public function __construct() 
    {
        $this->settingsByUser = array();
        return $this;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function getUserRepository()
    {
        return $this->userRepository;
    }
    
    /**
     * the $id for an entity 'setting' is the same as for the entity 'user'
     * @param type $id
     */
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
    
    public function find($id) {
        return $this->getSettingsByUser($user);
    }
    
    public function fetch() {
    }
    
    public function create($data = null) {
    }
    
    public function save(EntityInterface $entity) {
        
    }
    
    public function getEntityByStrategy($namespace) {
        $config = $this->getServiceLocator()->get('Config');
        if (array_key_exists('settings', $config)) {
            $settingsConfig = $config['settings'];
            if (array_key_exists('Settings', $namespace)) {
                
            }
        }
        return Null;
    }
    
}