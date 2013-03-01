<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Model;

use Core\Model\AbstractModel;

/**
 *
 */
class UserModel extends AbstractModel implements UserModelInterface
{
    
    protected $_email;
    
    public function setEmail($email) {
        $this->_email = (String) $email;
    }
    
    public function getEmail() {
        return $this->_email;
    }
}