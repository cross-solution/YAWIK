<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UpdatePermissionsSubscriber.php */ 
namespace Jobs\Repository\Event;

use Core\Repository\DoctrineMongoODM\Event\AbstractUpdatePermissionsSubscriber;

class UpdatePermissionsSubscriber extends AbstractUpdatePermissionsSubscriber
{
    protected $repositoryName = 'Jobs/Job';
}

