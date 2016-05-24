<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Cv\Form;

use Core\Form\SummaryForm;

class CvForm extends SummaryForm
{
    protected $baseFieldset = 'CvFieldset';

    protected $displayMode = self::DISPLAY_SUMMARY;
}
