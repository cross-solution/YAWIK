<?php

namespace Settings\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Stdlib\Hydrator\ClassMethods;
use Settings\Entity\Settings as SettingsEntity;
use Zend\Stdlib\Hydrator\ArraySerializable;

abstract class FormAbstract extends Form implements ServiceLocatorAwareInterface {
	
	protected $forms;

	public function __construct($name = null) {
		parent::__construct('settings');
		$this->setAttribute('method', 'post');
                $this->setBindOnValidate(Form::BIND_ON_VALIDATE);
	
		// 'action' => $this->url('lang/settings', array(), true),
	}
	
        /**
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
         * @return \Settings\Form\Settings
         */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->forms = $serviceLocator;
		return $this;
	}
	
	public function getServiceLocator()
	{
		return $this->forms;
	}
    
    public function getHydrator() {
        if (!$this->hydrator) {
            $hydrator = new ArraySerializable();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    protected function getPlugin($name) {
        $plugin = Null;
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

    public function init() {

        $this->setName('setting-core');
        
        $pluginUrl = $this->getPlugin('url');
        $url = call_user_func_array($pluginUrl, array(null, array('lang' => 'de')));
        $this->setAttribute('action', $url);
        
        //->setHydrator(new ModelHydrator())
        //->setObject(new SettingsEntity());
        
        $coreFieldset = $this->getCoreFieldset();
        if (isset($coreFieldset)) {
            $this->add($this->forms->get($coreFieldset)
                            ->setUseAsBaseFieldset(true)
            );
        }
                
        $this->add($this->forms->get('DefaultButtonsFieldset'));
    }
    
    abstract public function getCoreFieldset();
}
