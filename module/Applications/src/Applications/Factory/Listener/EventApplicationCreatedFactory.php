<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Factory\Listener;

use Applications\Listener\EventApplicationCreated;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class EventApplicationCreatedFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mailService     = $serviceLocator->get('Core/MailService');
        $listener        = new EventApplicationCreated($mailService);
        return $listener;
    }


}