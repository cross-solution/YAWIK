<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** UpdatePermissionsSubscriber.php */
namespace Applications\Repository\Event;

use Core\Repository\DoctrineMongoODM\Event\AbstractUpdatePermissionsSubscriber;

/**
 * class for updating permissions of an application
 */
class UpdatePermissionsSubscriber extends AbstractUpdatePermissionsSubscriber
{
    /**
     * repository for saving permissions
     */
    protected $repositoryName = 'Applications/Application';
}
