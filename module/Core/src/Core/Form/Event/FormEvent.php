<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Form\Event;

use Core\Form\Container;
use Zend\EventManager\Event;
use Zend\EventManager\Exception;
use Zend\Form\FormInterface;

/**
 * An event class to handle all kinds of form events.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.24
 */
class FormEvent extends Event
{
    /**#@+
     * @var string
     */

    /**
     * Event triggered while form initializes.
     */
    const EVENT_INIT = 'INIT';

    /**
     * Event triggered when data is set to the form.
     */
    const EVENT_SET_DATA = 'SET_DATA';

    /**
     * Event triggered when a form parameter is set.
     */
    const EVENT_SET_PARAM = 'SET_PARAM';

    /**
     * Event triggered when the form validates.
     */
    const EVENT_VALIDATE = 'VALIDATE';

    /**
     * Event triggered when the associated object is set. Either through binding or explicitely setObject.
     */
    const EVENT_SET_OBJECT = 'SET_OBJECT';

    /**#@-*/

    /**
     * The form instance triggered this event.
     *
     * @var FormInterface|Container
     */
    protected $form;

    /**
     * Gets the form instance.
     *
     * @return Container|FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Sets the form instance.
     *
     * @param FormInterface|Container $form
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setForm($form)
    {
        if (!$form instanceOf FormInterface && !$form instanceOf Container) {
            throw new \InvalidArgumentException('Form must either implement \Zend\Form\FormInterface or extend from \Core\Form\Container');
        }

        $this->form = $form;

        return $this;
    }

    public function setTarget($target)
    {
        if ($target instanceOf FormInterface || $target instanceOf Container) {
            $this->setForm($target);
        }
        parent::setTarget($target);
        
        return $this;
    }

    public function setParams($params)
    {
        parent::setParams($params);

        $form = $this->getParam('form');
        if ($form instanceOf FormInterface || $form instanceof Container) {
            $this->setForm($form);
        }

        return $this;
    }


}