<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\SummaryForm;

/**
 * Form of the customer note.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class CustomerNote extends SummaryForm
{
    protected $baseFieldset = 'Jobs/CustomerNoteFieldset';

    protected $label = /*@translate*/ 'Customer note';
    
    protected $displayMode = self::DISPLAY_SUMMARY;
}
