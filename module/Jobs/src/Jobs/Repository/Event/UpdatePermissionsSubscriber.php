<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UpdatePermissionsSubscriber.php */
namespace Jobs\Repository\Event;

use Core\Repository\DoctrineMongoODM\Event\AbstractUpdatePermissionsSubscriber;

/**
 * go to the parent-class for a closer documentation, there you also find all the logic
 * this subscriber has to be announced in the configuration-file
 *
 * Class UpdatePermissionsSubscriber
 * @package Jobs\Repository\Event
 */
class UpdatePermissionsSubscriber extends AbstractUpdatePermissionsSubscriber
{
    protected $repositoryName = 'Jobs/Job';
}
