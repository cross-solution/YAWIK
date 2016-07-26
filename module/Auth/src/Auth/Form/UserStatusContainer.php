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
 * Form container for user status.
 *
 * @author fedys
 */
class UserStatusContainer extends Container implements ViewPartialProviderInterface
{
    
    /**
     * View partial name.
     * @var string
     */
    protected $partial = 'auth/form/user-status-container';
    
    /**
     * @see \Core\Form\ViewPartialProviderInterface::getViewPartial()
     */
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    /**
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
        $this->setForms([
            'status' => [
                'type' => 'Auth/UserStatus',
                'property' => true
            ]
        ]);
    }
}
