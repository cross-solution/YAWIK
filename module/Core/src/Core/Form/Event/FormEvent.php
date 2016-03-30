<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Event;

use Core\Form\Container;
use Zend\EventManager\Event;
use Zend\EventManager\Exception;
use Zend\Form\FormInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class FormEvent extends Event
{
    const EVENT_INIT = 'INIT';
    const EVENT_SET_DATA = 'SET_DATA';
    const EVENT_SET_PARAM = 'SET_PARAM';
    const EVENT_VALIDATE = 'VALIDATE';

    protected $form;

    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setTarget($target)
    {
        if ($target instanceOf FormInterface || $target instanceOf Container) {
            $this->setForm($target);
        }

        return parent::setTarget($target);
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