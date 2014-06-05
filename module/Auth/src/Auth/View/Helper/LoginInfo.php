<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth view helper */
namespace Auth\View\Helper;
use Zend\View\Helper\AbstractHelper;

class LoginInfo extends AbstractHelper
{
     public function __invoke() {
         $values = array();
         return $this->getView()->render('auth/index/login-info', $values);
     }
}