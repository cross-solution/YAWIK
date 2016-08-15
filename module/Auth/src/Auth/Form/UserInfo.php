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
class UserInfo extends SummaryForm
{
    protected $wrapElements = false;
    protected $baseFieldset = 'Auth/UserInfoFieldset';

    
    /**
     * {@inheritDoc}
     */
    protected $displayMode = self::DISPLAY_SUMMARY;
    
    public function init()
    {
        $this->setLabel(/*@translate*/ 'Personal informations');
        parent::init();
    }
}
