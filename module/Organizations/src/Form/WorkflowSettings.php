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
 * Form for managing workflow.
 *
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @since 0.25
 */
class WorkflowSettings extends SummaryForm
{
    protected $baseFieldset = 'Organizations/WorkflowSettingsFieldset';

    protected $displayMode = self::DISPLAY_SUMMARY;
}
