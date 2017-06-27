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

use Core\Form\Container;
use Core\Form\ViewPartialProviderInterface;

/**
 * Form container for user informations.
 *
 * Combines user data form with user image upload,
 * provides a view partial to render the forms side by side.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserInfoContainer extends Container implements ViewPartialProviderInterface
{
    
    /**
     * View partial name.
     * @var string
     */
    protected $partial = 'auth/form/user-info-container';
    
    /**
     * {@inheritDoc}
     * @return UserInfoContainer
     * @see \Core\Form\ViewPartialProviderInterface::getViewPartial()
     */
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\ViewPartialProviderInterface::setViewPartial()
     */
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    
    /**
     * Initializes the container.
     *
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setForms(
            array(
            'info' => array(
                'type' => 'Auth/UserInfo',
                'property' => true,
	            'use_post_array' => true
            ),
            'image' => array(
                'type' => 'Auth/UserImage',
                'property' => true,
                'use_files_array' => true,
            ),
            )
        );
    }
}
