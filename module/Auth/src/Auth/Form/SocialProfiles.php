<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth forms */ 
namespace Auth\Form;

use Core\Form\BaseForm;

/**
 * Formular for adding social profiles.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class SocialProfiles extends BaseForm
{
    /**
     * Label for rendering purposes.
     * @var string
     */
    protected $label = /*@translate*/ 'Social Profiles';
    
    protected $baseFieldset = array(
        'type' => 'Auth/SocialProfilesFieldset',
        'options' => array(
            'profiles' => array(
                'facebook' => 'Facebook',
                'xing'     => 'Xing',
                'linkedin' => 'LinkedIn'
            ),
            'renderFieldset' => true,

        ),
    );
    
    /**
     * {@inheritDoc}
     * 
     * This method is a no-op, as we do not need a button fieldset.
     * @see \Core\Form\BaseForm::addButtonsFieldset()
     */
    protected function addButtonsFieldset()
    { }
}
