<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Rating.php */ 
namespace Core\Form\Element;


use Zend\Form\Element;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PolicyCheckFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services     = $serviceLocator->getServiceLocator();
        $translator   = $services->get('translator');
        $policyCheck  = $this->getElement();
        $policyCheck->injectTranslator($translator);

        return $policyCheck;
    }
    
    protected function getElement() {
        return new PolicyCheck();
    }
}