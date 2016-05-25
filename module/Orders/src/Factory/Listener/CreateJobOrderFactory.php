<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Factory\Listener;

use Orders\Listener\CreateJobOrder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class CreateJobOrderFactory implements FactoryInterface
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
        $options         = $serviceLocator->get('Orders/Options/Module');
        $providerOptions = $serviceLocator->get('Jobs/Options/Provider');
        $priceFilter     = $serviceLocator->get('filtermanager')->get('Jobs/ChannelPrices');
        $repositories    = $serviceLocator->get('repositories');
        $repository      = $repositories->get('Orders');
        $draftrepo       = $repositories->get('Orders/InvoiceAddressDraft');
        $listener        = new CreateJobOrder($options, $providerOptions, $priceFilter, $repository, $draftrepo);

        return $listener;
    }


}