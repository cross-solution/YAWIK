<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Form;

use Core\Form\SummaryForm;

/**
 * Form for managing employees.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class Employees extends SummaryForm
{
    protected $baseFieldset = 'Organizations/EmployeesFieldset';

    protected $displayMode = self::DISPLAY_SUMMARY;

//    public function addButtonsFieldset()
//    {
//        return; // No buttons.
//    }



}