<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** UniqueApplyIdFactory.php */ 
namespace Auth\Form\Validator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UniqueGroupNameFactory implements FactoryInterface
{
    
    protected $options = array();
    
    public function __construct($options) 
    {
        $this->options['allowName'] = isset($options['allowName']) ? $options['allowName'] : null;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services  = $serviceLocator->getServiceLocator();
        $auth      = $services->get('AuthenticationService');
        $user      = $auth->getUser();
        $options   = $this->options;
        $options['user'] = $user;
        $validator = new UniqueGroupName($options);

        return $validator;
    }
}

