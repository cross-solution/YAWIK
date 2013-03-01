<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Adapter\ModelMapper;

use Auth\Model\UserInterface;

class Xing implements ModelMapperInterface
{
    
    public function map(\Hybrid_User_Profile $profile, UserInterface $user)
    {
        $email = isset($profile->emailVerified) && !empty($profile->emailVerified)
              ? $profile->emailVerified
              : $profile->email;
              
        $user->setData(array(
            'email' => $email,
            'firstName' => $profile->firstName,
            'lastName' => $profile->lastName,
            'displayName' => $profile->displayName,
        ));
        
        $user->setXingInfo((array) $profile); 
    }
}