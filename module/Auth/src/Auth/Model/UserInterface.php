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
interface UserInterface extends ModelInterface
{
    public function setEmail($email);
    public function getEmail();
    public function setFirstName($name);
    public function getFirstName();
    public function setLastName($name);
    public function getLastName();
    public function setDisplayName($name);
    public function getDisplayName();
    public function setFacebookInfo(array $info);
    public function getFacebookInfo();
    public function setLinkedInInfo(array $info);
    public function getLinkedInInfo();
    public function setXingInfo(array $info);
    public function getXingInfo();
}