<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper */
namespace Auth\Mapper;

use Core\Mapper\MapperInterface;

/**
 * User mapper interface
 */
interface UserMapperInterface extends MapperInterface
{
    /**
     * Finds an user by email address.
     * 
     * @param string $email
     * @return \Auth\Model\User
     */
    public function findByEmail($email);
}

