<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Auth\Form;

use Core\Form\Container;
use Zend\Form\FormInterface;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserInfoContainer extends Container
{
    
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        $this->get('info')->bind($object);
        return $this;
    }
    
    public function init()
    {
        $this->setName('user');
        $this->addLazy('Auth/UserInfo', 'info');
    }
}
