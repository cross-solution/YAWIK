<?php
/**
 * Cross Applicant Management
 *
 * @filesource    
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth adapter modelmapper */
namespace Auth\Adapter\ModelMapper;

use Auth\Model\UserInterface;

/**
 * Mapper to map a Hybridauth Xing user profile to an user model.
 */
class Xing implements ModelMapperInterface
{
    
    /**
     * {@inheritdoc}
     * @see \Auth\Adapter\ModelMapper\ModelMapperInterface::map()
     */
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