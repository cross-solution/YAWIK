<?php
/**
 * 
 */

namespace Settings\Settings;

//use Settings\Entity\Settings as SettingsEntity;
use Settings\Repository\Settings as SettingsRepository;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Exception;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Entity\UserInterface;

class Settings extends AbstractPlugin
{
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;
    protected $settingsByUser;
    protected $auth;
    protected $repository;
    
    public function __construct() {
        $this->settingsByUser = array();
    }
    
    
    public function __invoke($module = null, $user = null)
    {
        if ($module instanceOf UserInterface) {
            $user = $module;
            $module = null;
        }
        if (!isset($user)) {
            $auth = $this->auth;
            $user = $auth->getIdentity();
        }
        $settingsEntity = $this->repository->getSettingsByUser($user);
        
        $settings = Null;
        $controller = explode('\\', get_class($this->getController()));
        // ToDo: der Namespace kann auch aus $module kommen
        $namespace = strtolower($controller[0]);
        if (isset($settingsEntity)) {
            // Die Settings für diesen User existieren
            if (!empty($module) && strcasecmp($module,$namespace) != 0) {
                $settings = $settingsEntity->getByNamespace(strtolower($module));
                if (isset($settings)) {
                    $settings->setAccessWrite(False);
                }
            }
            else {
                $settings = $settingsEntity->getByNamespace($namespace);
            }
        }
        return $settings;
    }
    
    public function setLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;
    }
    
    public function setRepository(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }
    
     /**
     * Get the locator
     *
     * @return ServiceLocatorInterface
     * @throws Exception\DomainException if unable to find locator
     */
    protected function getLocator()
    {
        return $this->serviceManager;
    }
        
}
