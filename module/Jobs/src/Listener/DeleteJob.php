<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Listener;

use Auth\Entity\UserInterface;
use Core\Listener\Events\AjaxEvent;
use Jobs\Repository\Job;
use Zend\Permissions\Acl\AclInterface;

/**
 * Ajax event listener for deleting jobs.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class DeleteJob
{
    /**
     * Job repository
     *
     * @var \Jobs\Repository\Job
     */
    private $repository;

    /**
     * Current user
     *
     * @var \Auth\Entity\UserInterface
     */
    private $user;

    /**
     * ACL service
     *
     * @var \Zend\Permissions\Acl\AclInterface
     */
    private $acl;

    /**
     * @param Job           $repository
     * @param UserInterface $user
     * @param AclInterface  $acl
     */
    public function __construct(Job $repository, UserInterface $user, AclInterface $acl)
    {
        $this->repository = $repository;
        $this->user       = $user;
        $this->acl        = $acl;
    }

    /**
     * Delete a job via ajax call.
     *
     * Returns an array with two or three keys:
     * - "success": a boolean flag whether the call was successful or not.
     * - "status": a text flag. "fail" or "OK"
     * - "error": An error message.
     *
     * @param AjaxEvent $event
     *
     * @return array
     */
    public function __invoke(AjaxEvent $event)
    {
        $request = $event->getRequest();
        $query   = $request->getQuery();
        $id      = $query->get('id');
        
        if (!$id) {
            return ['success' => false, 'status' => 'fail', 'error' => 'No id provided'];
        }
        
        $job = $this->repository->find($id);
        
        if (!$job || !$this->acl->isAllowed($this->user, $job, 'delete')) {
            return ['success' => false, 'status' => 'fail', 'error' => !$job ? 'Job not found.' : 'No permissions.'];
        }
        
        $job->delete();

        return ['success' => true, 'status' => 'OK'];
    }
}
