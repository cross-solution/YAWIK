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
    protected $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function __invoke(FormEvent $event)
    {
        /* @var \Jobs\Form\Job $container */
        $container = $event->getForm();

        $container->setForm(
            'invoice',
            [
                'priority' => -10,
                'label' => /*@translate*/ 'Invoice Address',
                'entity' => 'orders.invoice',
                'property' => true,
                'forms' => [
                    'invoiceAddress' => [
                        'label' => 'Invoice Address',
                        'type' => 'Orders/JobInvoiceAddress',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Invoice address description.',
                        ],
                    ],
                ],
            ]
        );

        $previewSpec = $container->getForm('preview', false);
        $previewSpec['priority'] = -20;
        $container->setForm('preview', $previewSpec);
        $container->setEntity($this->entity, 'orders.invoice');
    }
    
}