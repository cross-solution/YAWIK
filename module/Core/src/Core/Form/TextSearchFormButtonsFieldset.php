<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Form;

use Zend\Form\Element;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;

/**
 * Fieldset for the buttons of a TextSearchForm
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
class TextSearchFormButtonsFieldset extends Fieldset
{
    public function init()
    {
        $this->addDefaultButtons();
    }

    /**
     * Adds the default buttons.
     *
     */
    protected function addDefaultButtons()
    {
        $this->addButton( /*@translate*/ 'Search', -1000, 'submit'
        );
        $this->addButton( /*@translate*/ 'Clear', -1100, 'reset'
        );
    }


    /**
     * Adds a button
     *
     * @param string $label
     * @param int    $priority
     * @param string $type
     *
     * @return Fieldset|FieldsetInterface
     */
    public function addButton($label, $priority = 0, $type = "button")
    {
        if (is_array($label)) {
            $name  = $label[0];
            $label = $label[1];
        } else {
            $name = strtolower(str_replace(' ', '_', $label));
        }

        $spec = [
            'type'       => 'Button',
            'name'       => $name,
            'options'    => [
                'label' => $label,
            ],
            'attributes' => [
                'class' => 'btn btn-' . ('submit' == $type ? 'primary' : 'default'),
                'type'  => $type,
            ],
        ];


        return $this->add($spec, ['priority' => $priority]);
    }

    /**
     * Sets the column span of the button group.
     *
     * Only used, if the buttons aren't rendered on an element.
     *
     * @param int $span
     *
     * @return self
     */
    public function setSpan($span)
    {
        $this->setOption('span', $span);

        return $this;
    }

    /**
     * Gets the column span of the button group.
     *
     * @return int
     */
    public function getSpan()
    {
        return $this->getOption('span') ? : 12;
    }
}