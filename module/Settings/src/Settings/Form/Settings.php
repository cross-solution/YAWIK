<?php

namespace Settings\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods;
use Settings\Entity\Settings as SettingsEntity;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Hydrator\ArraySerializable;

class Settings extends Form {

    public function __construct($name = null) {
        parent::__construct('settings');
        $this->setAttribute('method', 'post');
        
        // 'action' => $this->url('lang/settings', array(), true),
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

        $this->setName('Settings');
        $plugin = $this->getPlugin('url');
        $url = call_user_func_array($plugin, array(null, array('lang' => 'de')));
        $this->setAttribute('action', $url);
        
        //->setHydrator(new ModelHydrator())
        //->setObject(new SettingsEntity());

        /*
          $this->add(array(
          'type' => 'Hidden',
          'name' => 'jobid',
          ));
         */

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'language',
            'options' => array(
                'label' => 'Choose your Language',
                'value_options' => array(
                    'en' => /* @translate */ 'English',
                    'de' => /* @translate */ 'German',
                ),
            ),
            'attributes' => array(
            //'id' => 'contact-title',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Email-Adresse'
            ),
            'attributes' => array(
            //'id' => 'contact-title',
            )
        ));
        

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'test',
            'options' => array(
                'label' => 'Test'
            ),
            'attributes' => array(
            //'id' => 'contact-title',
            )
        ));

        $this->add(
                array(
                    'name' => 'send',
                    'attributes' => array(
                        'type' => 'submit',
                        'value' => 'Submit',
                    ),
                )
        );
    }

}

