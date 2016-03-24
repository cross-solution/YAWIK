<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Form;

use Core\Form\Event\FormEvent;
use Core\Form\SummaryForm;
use Zend\EventManager\EventManagerInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class InvoiceAddress extends SummaryForm
{
    protected $baseFieldset = 'Orders/InvoiceAddressFieldset';

    /**
     * The event manager
     *
     * @var EventManagerInterface
     */
    protected $events;

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;

        return $this;
    }

    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new \Zend\EventManager\EventManager());
        }

        return $this->events;
    }

    public function isValid()
    {
        $valid = parent::isValid();

        $events = $this->getEventManager();
        $events->trigger(FormEvent::EVENT_VALIDATE, $this, [ 'isValid' => $valid ]);

        return $valid;
    }


}