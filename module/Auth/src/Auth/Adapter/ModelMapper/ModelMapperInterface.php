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
 * Model mapper interface.
 * 
 * Maps Hybrid_User_Profile to domain models.
 * 
 */
interface ModelMapperInterface
{
    /**
     * Maps an Hybrid_User_Profile to an user model.
     * 
     * @param \Hybrid_User_Profile $profile
     * @param UserInterface $user
     * 
     */
    public function map(\Hybrid_User_Profile $profile, UserInterface $user);
}