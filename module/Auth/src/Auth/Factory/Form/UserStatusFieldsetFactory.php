<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Form\UserStatusFieldset;

class UserStatusFieldsetFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $translator = $services->get('Translator');
        $statusOptions = (new \Auth\Entity\Status())->getOptions($translator);
        $fieldset = new UserStatusFieldset();
        $fieldset->setStatusOptions($statusOptions);
        
        return $fieldset;
    }
}
