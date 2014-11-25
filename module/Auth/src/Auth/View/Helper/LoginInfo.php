<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth view helper */
namespace Auth\View\Helper;
use Zend\View\Helper\AbstractHelper;

/**
 * Class LoginInfo
 * @package Auth\View\Helper
 */
class LoginInfo extends AbstractHelper
{
    /**
     * @param array $values for the template (should include a value for 'lang')
     * @return string
     */
    public function __invoke($values = array()) {
         return $this->getView()->render('auth/index/login-info', $values);
     }
}