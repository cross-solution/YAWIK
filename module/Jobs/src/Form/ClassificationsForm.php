<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Form;

use Core\Form\SummaryForm;

/**
 * Form for the categories management.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ClassificationsForm extends SummaryForm
{
    protected $baseFieldset = 'Jobs/ClassificationsFieldset';

    protected $label = /*@translate*/ 'Classifications';
}