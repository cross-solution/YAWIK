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

use Core\Form\SummaryForm;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ClassificationsForm extends SummaryForm
{
    protected $baseFieldset = 'Jobs/ClassificationsFieldset';

    protected $label = /*@translate*/ 'Classifications';
}