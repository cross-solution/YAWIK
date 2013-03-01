<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Mapper;

use Core\Mapper\MapperInterface;

interface UserMapperInterface extends MapperInterface
{
    public function findByEmail($email);
}

