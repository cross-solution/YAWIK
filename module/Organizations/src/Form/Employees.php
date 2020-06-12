<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
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
}
