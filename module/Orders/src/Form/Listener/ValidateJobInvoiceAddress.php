<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Form\Listener;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Event\FormEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Session\Container;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ValidateJobInvoiceAddress
{
    public function __invoke(FormEvent $event)
    {
        $invoiceAddress = $event->getForm()->getForm('invoice.invoiceAddress')->getObject();

        foreach (['name', 'company', 'street', 'city', 'vatIdNumber'] as $field) {
            $value = $invoiceAddress->{"get$field"}();
            if (empty($value)) {
                return  /*@translate*/ 'Please fill in and check your invoice address.';
            }
        }
    }
}