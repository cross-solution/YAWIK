<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Form\Hydrator\UserPasswordHydrator;

class UserPassword extends Form implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $forms;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->forms = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->forms;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new UserPasswordHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('user-password-form');

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
        ));
        
        $this->add(
            $this->forms->get('Auth/UserPasswordFieldset')
        );
        
        $this->add($this->forms->get('submitField'));
    }
    
}