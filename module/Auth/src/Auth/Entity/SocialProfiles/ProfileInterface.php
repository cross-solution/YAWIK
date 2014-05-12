<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ProfileInterface.php */ 
namespace Auth\Entity\SocialProfiles;

use Doctrine\Common\Collections\Collection;
use Core\Entity\EntityInterface;

interface ProfileInterface extends EntityInterface
{
    
    public function setName($name);
    public function getName();
    
    public function setData(array $data);
    public function getData();
    
    public function getLink();
    
    public function getEducations();
    
    public function getEmployments();
    
}

