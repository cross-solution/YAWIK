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

use Applications\Listener\StatusChange;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @todo write test 
 */
class StatusChangeFactory implements FactoryInterface
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
        $options         = $serviceLocator->get('Applications/Options');
        $mailService     = $serviceLocator->get('Core/MailService');
        $translator      = $serviceLocator->get('translator');
        $listener        = new StatusChange($options, $mailService, $translator);
        return $listener;
    }
}