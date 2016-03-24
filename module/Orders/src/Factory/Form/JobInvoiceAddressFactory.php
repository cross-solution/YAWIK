<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Factory\Form;

use Orders\Form\InvoiceAddress;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Orders\Entity\InvoiceAddress as InvoiceAddressEntity;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JobInvoiceAddressFactory implements FactoryInterface
{
    protected $options;


    public function __construct($options = [])
    {
        $this->options = $options;
    }

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
        $events = $services->get('Jobs/JobContainer/Events');
        //$router = $services->get('Router');
        //$action = $router->assemble([], ['name' => 'lang/orders/invoice-address']) . '?type=job';
        $invoice = new InvoiceAddress($this->options['name'], $this->options);
        $invoice//->setAttribute('action', $action)
                ->setEventManager($events);


        return $invoice;
    }


}