<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Factory\Form\Listener;

use Orders\Form\Listener\BindInvoiceAddressEntity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class BindInvoiceAddressEntityFactory implements FactoryInterface
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
        $repositories = $serviceLocator->get('repositories');
        $drafts       = $repositories->get('Orders/InvoiceAddressDraft');
        $orders       = $repositories->get('Orders');
        $callback     = function() use ($serviceLocator) { return $serviceLocator->get('Orders/Entity/JobInvoiceAddress'); };
        $listener     = new BindInvoiceAddressEntity($orders, $drafts, $callback);

        return $listener;

    }


}