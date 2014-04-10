<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** UpdatePermissionsSubscriber.php */ 
namespace Applications\Repository\Event;

use Core\Repository\DoctrineMongoODM\Event\AbstractUpdatePermissionsSubscriber;

class UpdatePermissionsSubscriber extends AbstractUpdatePermissionsSubscriber
{
    protected $repositoryName = 'Applications/Application';
}

