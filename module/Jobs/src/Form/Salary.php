<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

namespace Jobs\Form;

use Core\Form\SummaryForm;

/**
 * Defines the form for entering the job salary information
 *
 * @package Jobs\Form
 */
class Salary extends SummaryForm
{
    /**
     * Formular fields are defined in SalaryFieldset
     *
     * @var string
     */
    protected $baseFieldset = 'Jobs/SalaryFieldset';

    /**
     * Header of the formular box
     *
     * @var string
     */
    protected $label = /*@translate*/ 'Salary';

    /**
     * Hint, which representation to show in view
     *
     * @var string
     */
    protected $displayMode = self::DISPLAY_SUMMARY;
}
