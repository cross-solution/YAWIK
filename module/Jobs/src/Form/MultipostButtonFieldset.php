<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\SummaryFormButtonsFieldset;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class MultipostButtonFieldset extends SummaryFormButtonsFieldset
{
    public function init()
    {
        $this->add(
             array(
                 'type' => 'Core/Spinner-Submit',
                 'name' => 'calculate',
                 'options' => array(
                     'label' => /*@translate*/ 'Calculate price',
                 ),
                 'attributes' => array(
                     'id' => 'calculate',
                     'type' => 'button',
                     'value' => 'calculate',
                     'class' => 'mps-calculate btn btn-success btn-xs'
                 ),
             )
        );

        parent::init();

        $this->get('submit')->setAttribute('class', 'mps-submit sf-submit btn btn-primary btn-xs');
    }
}
