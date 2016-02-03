<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications forms */
namespace Applications\Form;

use Core\Form\SummaryForm;

/**
 * Form for base application data.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Base extends SummaryForm
{
    /**
     * Label for the form.
     *
     * @var string
     */
    protected $label        = /*@translate*/ 'Cover Letter';
    
    /**
     * {@inheritDoc}
     */
    protected $baseFieldset = array(
        'type' => 'Applications/BaseFieldset',
        'options' => array(
            'disable_capable' => array(
                'label' => 'Test label base form',
            ),
            'is_disable_capable' => false,
            'is_disable_elements_capable' => true,
        ),
    );
    
    /**
     * {@inheritDoc}
     */
    protected $displayMode = 'summary';
}
