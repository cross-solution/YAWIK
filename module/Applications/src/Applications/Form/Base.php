<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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
     * @var string
     */
    protected $label        = /*@translate*/ 'Summary';
    
    /**
     * {@inheritDoc}
     */
    protected $baseFieldset = 'Applications/BaseFieldset';
    
    /**
     * {@inheritDoc}
     */
    protected $displayMode = 'summary';
    
}
