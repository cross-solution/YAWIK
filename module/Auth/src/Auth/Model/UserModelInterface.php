<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Model;

use Core\Model\ModelInterface;

/**
 *
 */
interface UserModelInterface extends ModelInterface
{
    public function setEmail($email);
    public function getEmail();
}