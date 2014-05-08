<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ProfileInterface.php */ 
namespace Auth\Entity;

use Core\Entity\EntityInterface;

interface ProfileInterface extends EntityInterface
{
    public function setName($name);
    public function getName();
    public function setData(array $data);
    public function getData();
}

