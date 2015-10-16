<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Filter;

use Jobs\Filter\ChannelPrices;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ChannelPricesFactory implements FactoryInterface
{
    protected $filterClass = '\Jobs\Filter\ChannelPrices';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $providerOptions = $services->get('Jobs/Options/Provider');

        $class = $this->filterClass;
        $filter = new $class($providerOptions);

        return $filter;
    }


}