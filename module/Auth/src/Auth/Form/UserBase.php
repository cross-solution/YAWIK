<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Auth\Form;

use Core\Form\SummaryForm;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserBase extends SummaryForm
{
    protected $baseFieldset = 'Auth/UserBaseFieldset';
    protected $displayMode = self::DISPLAY_SUMMARY;
}
