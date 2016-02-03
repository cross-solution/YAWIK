<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueApplyIdFactory.php */
namespace Jobs\Form\Validator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UniqueApplyIdFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services     = $serviceLocator->getServiceLocator();
        $repositories = $services->get('repositories');
        $jobs         = $repositories->get('Jobs/Job');
        $validator    = new UniqueApplyId($jobs);

        return $validator;
    }
}
