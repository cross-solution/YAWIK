<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
     * @var bool
     */
    protected $useDefaultValidation = false;
    
    /**
     * {@inheritDoc}
     *
     * This method is a no-op, as we do not need a button fieldset.
     * @see \Core\Form\BaseForm::addButtonsFieldset()
     */
    protected function addButtonsFieldset()
    {
    }
    
    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return $this->useDefaultValidation ? \Laminas\Form\Form::isValid() : parent::isValid();
    }
    
	/**
	 * @param bool $bool
	 * @return SocialProfiles
	 */
	public function setUseDefaultValidation($bool)
	{
		$this->useDefaultValidation = (bool)$bool;
		
		return $this;
	}
}
