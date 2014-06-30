<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Applications\Form;

use Core\Form\Container;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */

class Apply extends Container
{
    public function init()
    {
        $this->setName('application');
        $this->addLazy('Auth/UserInfoContainer', 'contact');
    }
}
