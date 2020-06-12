<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace Organizations\Form;

use Core\Form\SummaryForm;

/**
 * Class OrganizationsProfileForm
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.30
 * @package Organizations\Form
 */
class OrganizationsProfileForm extends SummaryForm
{
    protected $baseFieldset = OrganizationsProfileFieldset::class;

    protected $displayMode = self::DISPLAY_SUMMARY;
}
