<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\SummaryForm;

/**
 * This form is used to configure the ATS settings of a job entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class AtsMode extends SummaryForm
{
    protected $displayMode = self::DISPLAY_SUMMARY;

    protected $baseFieldset = 'Jobs/AtsModeFieldset';

    public function init()
    {
        $this->setOptions(
            array(
            'headscript' => 'modules/Jobs/js/form.ats-mode.js',
            )
        );

        parent::init();
    }
}
