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

/**
 * Class OrganizationsContactForm
 *
 * @package Organizations\Form
 */
class OrganizationsContactForm extends SummaryForm
{
    protected $baseFieldset = 'Organizations/OrganizationsContactFieldset';

    protected $displayMode = self::DISPLAY_SUMMARY;
}
