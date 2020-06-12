<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Organizations\Form;

use Core\Form\SummaryForm;

class OrganizationsNameForm extends SummaryForm
{
    protected $baseFieldset = 'Organizations/OrganizationsNameFieldset';

    protected $displayMode = self::DISPLAY_SUMMARY;
}
