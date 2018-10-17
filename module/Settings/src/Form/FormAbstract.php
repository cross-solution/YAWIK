<?php

namespace Settings\Form;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;
use Zend\Hydrator\ArraySerializable;

abstract class FormAbstract extends Form
{
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $formManager;

    public function __construct(ServiceLocatorInterface $formManager, $name = null)
    {
        parent::__construct('settings');
        $this->formManager = $formManager;
        $this->setAttribute('method', 'post');
        $this->setBindOnValidate(Form::BIND_ON_VALIDATE);
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new ArraySerializable();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    protected function getPlugin($name)
    {
        $plugin = null;
        $factory = $this->getFormFactory();
        $formElementManager = $factory->getFormElementManager();
        if (isset($formElementManager)) {
            $serviceLocator = $formElementManager->getServiceLocator();
            $viewhelpermanager = $serviceLocator->get('viewhelpermanager');
            if (isset($viewhelpermanager)) {
                $plugin = $viewhelpermanager->get($name);
            }
        }
        return $plugin;
    }

    public function init()
    {
        $this->setName('setting-core');
        
        $pluginUrl = $this->getPlugin('url');
        $url = call_user_func_array($pluginUrl, array(null, array(), null, true));
        $this->setAttribute('action', $url);
        
        //->setHydrator(new ModelHydrator())
        //->setObject(new SettingsEntity());
        
        $coreFieldset = $this->getCoreFieldset();
        if (isset($coreFieldset)) {
            $this->add(
                $this->formManager->get($coreFieldset)
                            ->setUseAsBaseFieldset(true)
            );
        }
                
        $this->add($this->formManager->get('DefaultButtonsFieldset'));
    }
    
    abstract public function getCoreFieldset();
    
    /**
     * @param ServiceLocatorInterface $formManager
     * @return FormAbstract
     */
    public static function factory(ServiceLocatorInterface $formManager)
    {
        return new static($formManager);
    }
}
