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
class ValidateJobInvoiceAddress implements ListenerAggregateInterface
{
    /**
     *
     *
     * @var array
     */
    protected $listeners;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(FormEvent::EVENT_VALIDATE, [ $this, 'onValidate' ]);
        $this->listeners[] = $events->attach('ValidateJob', [ $this, 'onValidateJob' ]);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $i => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$i]);
            }
        }

        return empty($this->listeners);
    }

    public function onValidate(FormEvent $event)
    {
        if (!$event->getParam('isValid')) {
            return;
        }

        $form = $event->getForm();
        $hydrator = new EntityHydrator();
        $jobId = $form->getInputFilter()->getRawValue('job');
        $session = new Container('Orders_JobInvoiceAddress_' . $jobId);
        $session->values = $hydrator->extract($form->getObject());
    }

    public function onValidateJob(FormEvent $event)
    {
        $jobId   = $event->getForm()->getEntity('*')->getId();
        $session = new Container('Orders_JobInvoiceAddress_' . $jobId);

        if (empty($session->values)) {
            return /*@translate*/ 'Please fill in and check your invoice address.';
        }
    }
}