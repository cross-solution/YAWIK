<?php

namespace Auth\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Usergroup extends AbstractPlugin
{
    public function __invoke() {
        return $this;
    }
    
    public function getSettings() {
        // this is preliminary until we have the aility for group-settings
        $services = $this->getController()->getServiceLocator();
        $config = $services->get('config');
        if (array_key_exists('group',$config)) {
            return $config['group'];
        }
        return array();
    }
}