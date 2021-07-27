<?php

namespace Settings\Repository;

use Auth\Entity\UserInterface;
use Core\Entity\IdentifiableEntityInterface;
use Doctrine\ODM\MongoDB\LockMode;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Mvc\MvcEvent;
use Settings\Entity\SettingsContainer as SettingsEntity;
use Core\Repository\AbstractRepository;

class Settings extends AbstractRepository
{
    protected $userRepository;
    protected $settingsByUser;
    protected $container;
    
    public function __construct()
    {
        $this->settingsByUser = array();
    }
    
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    protected function getContainer()
    {
        return $this->container;
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
     * @param UserInterface $user
     * @return mixed|SettingsEntity|null
     */
    public function getSettingsByUser($user)
    {
        $userId = $user;
        $userEntity = null;
        if ($user instanceof IdentifiableEntityInterface) {
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
            $this->settingsByUser[$userId] = new SettingsEntity();
            $this->settingsByUser[$userId]->setData($settingsData)->spawnAsEntities();
            return $this->settingsByUser[$userId];
        }
        return null;
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
    
    public function find($user, $lockMode = LockMode::NONE, ?int $lockVersion = null): ?object
    {
        return $this->getSettingsByUser($user);
    }
    
    public function getEntityByStrategy($namespace)
    {
        $configAccess = $this->getContainer()->get('ConfigAccess');
        $settings = $configAccess->getByKey('settings');
        if (array_key_exists($namespace, $settings) && array_key_exists('entity', $settings[$namespace])) {
            $entity = new $settings[$namespace]['entity'];
            $entity->setConfig($settings[$namespace]);
            return $entity;
        }
        return null;
    }
    
    public function getFormular($formular = null)
    {
        $form = null;
        if (isset($formular) && is_string($formular)) {
            $formElementManager = $this->getContainer()->get('FormElementManager');
            if ($formElementManager->has($formular)) {
                $form = $formElementManager->get($formular);
            }
            if (!isset($form)) {
                if ($this->getContainer()->has($formular)) {
                    $form = $this->getContainer()->get($formular);
                }
            }
        }
        return $form;
    }
}
