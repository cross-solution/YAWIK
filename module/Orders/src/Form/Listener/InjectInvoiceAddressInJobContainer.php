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

use Core\Form\Event\FormEvent;
use Orders\Entity\InvoiceAddress;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class InjectInvoiceAddressInJobContainer
{
    public function __invoke(FormEvent $event)
    {
        /* @var \Jobs\Form\Job $container */
        $container = $event->getForm();

        $container->setForm(
            'invoice',
            [
                'priority' => -10,
                'label' => /*@translate*/ 'Invoice Address',
                'entity' => false, //'orders.invoice',
                'property' => false,
                'forms' => [
                    'invoiceAddress' => [
                        'label' => 'Invoice Address',
                        'type' => 'Orders/JobInvoiceAddress',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Please leave your contact completely . We need this data for business correspondence and coordination of questions about the job posting . These data are of course not be published.',
                        ],
                    ],
                ],
            ]
        );

        $previewSpec = $container->getForm('preview', false);
        $previewSpec['priority'] = -20;
        $container->setForm('preview', $previewSpec);
    }
    
}