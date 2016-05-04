<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Traversable;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class TextSearchFormButtonsFieldset extends Fieldset
{
    public function init()
    {
        $this->addDefaultButtons();
    }

    protected function addDefaultButtons()
    {
        $this->addButton(/*@translate*/ 'Search', -1000, 'submit');
        $this->addButton(/*@translate*/ 'Clear', -1100, 'reset');
    }


    public function addButton($label, $priority = 0, $type = "button")
    {
        if (is_array($label)) {
            $name = $label[0];
            $label = $label[1];
        } else {
            $name = strtolower(str_replace(' ', '_', $label));
        }

        $spec = [
            'type' => 'Button',
            'name' => $name,
            'options' => [
                'label' => $label,
            ],
            'attributes' => [
                'class' => 'btn btn-' . ('submit' == $type ? 'primary' : 'default'),
                'type' => $type,
            ],
        ];


        return $this->add($spec, [ 'priority' => $priority ]);
    }

    public function setSpan($span)
    {
        $this->setOption('span', $span);

        return $this;
    }

    public function getSpan()
    {
        return $this->getOption('span') ?: 12;
    }
}