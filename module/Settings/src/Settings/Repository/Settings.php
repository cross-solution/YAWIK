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
        $userId = $user;
        $userEntity = Null;
        if ($user instanceOf EntityInterface) {
            $userId = $user->getId();
            $userEntity = $user;
        }
        if (isset($this->settingsByUser[$userId])) {
            return $this->settingsByUser[$user];
        }
        if (empty($userEntity)) {
            $UserRepository = $this->getUserRepository();
            $userEntity = $UserRepository->find($userId);
        }
        
        if (isset($userEntity)) {
            $settingsData = $userEntity->getSettings();
            $this->settingsByUser[$userId] = new SettingsEntity($this);
            $this->settingsByUser[$userId]->setData($settingsData)->spawnAsEntities(); 
            return $this->settingsByUser[$userId];
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
        $configAccess = $this->getServiceLocator()->get('ConfigAccess');
        $settings = $configAccess->getByKey('settings');
        if (array_key_exists($namespace, $settings) && array_key_exists('entity', $settings[$namespace])) {
            $entity = new $settings[$namespace]['entity'];
            $entity->setConfig($settings[$namespace]);
            return $entity;
        }
        return Null;
    }
    
    public function getFormular($formular = Null) {
        $form = Null;
        if (isset($formular) && is_string($formular)) {
            $formElementManager = $this->getServiceLocator()->get('FormElementManager');
            if ($formElementManager->has($formular)) {
                $form = $formElementManager->get($formular);
            }
            if (!isset($form)) {
                if ($this->getServiceLocator()->has($formular)) {
                    $form = $this->getServiceLocator()->get($formular);
                }
            }
        }
        return $form;
    }
    
}