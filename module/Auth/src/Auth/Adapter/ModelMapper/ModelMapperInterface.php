<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Adapter\ModelMapper;

use Auth\Model\UserInterface;

interface ModelMapperInterface
{
    public function map(\Hybrid_User_Profile $profile, UserInterface $user);
}