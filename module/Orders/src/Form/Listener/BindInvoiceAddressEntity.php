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

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class BindInvoiceAddressEntity 
{

    protected $repository;

    protected $createEntityCallback;

    public function __construct($repository, $callback)
    {
        $this->repository = $repository;
        $this->createEntityCallback = $callback;
    }

    public function __invoke(FormEvent $event)
    {
        if ('job' != $event->getParam('param_name') || $event->getForm()->getObject()) {
            return;
        }

        $jobId  = $event->getParam('param_value');
        $entity = $this->repository->findByJobId($jobId);

        if ($entity) {
            $invoiceAddress = $entity->getInvoiceAddress();

        } else {
            $callback = $this->createEntityCallback;
            $invoiceAddress = $callback();
            $entity = $this->repository->create(
                [
                    'jobId'  => $jobId,
                    'invoiceAddress' => $invoiceAddress
                ], true
            );

            /* If form is not instantiated until rendering, the PersistenceListener has already run
             * at this time. So we need to manually store the entity to be sure it is persisted.
             *
             */
            $this->repository->store($entity);
        }

        $form = $event->getForm();
        $form->bind($invoiceAddress);
    }
}