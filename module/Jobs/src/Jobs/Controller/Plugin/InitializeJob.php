<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Jobs\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Repository\RepositoryService;
use Auth\AuthenticationService;
use Zend\Mvc\Controller\Plugin\Params;
use Acl\Service\Acl;

/**
 * Class InitializeJob
 *
 * @package Jobs\Controller\Plugin
 */
class InitializeJob extends AbstractPlugin {

    /**
     * @var RepositoryService
     */
    protected $repositoryService;

    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var \Acl\Controller\Plugin\Acl
     */
    protected $acl;

    public function __construct(RepositoryService $repositoryService,AuthenticationService $auth, Acl $acl) {
        $this->repositoryService=$repositoryService;
        $this->auth=$auth;
        $this->acl=$acl;
    }

    public function __invoke()
    {
        return $this;
    }

    /**
     * @param Params $params
     * @param bool   $allowDraft
     *
     * @return \Jobs\Entity\Job|object
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    public function get(Params $params,$allowDraft = false)
    {
        /* @var \Jobs\Repository\Job $jobRepository */
        $jobRepository  = $this->repositoryService->get('Jobs/Job');
        // @TODO three different method to obtain the job-id ?, simplify this
        $id_fromRoute   = $params('id', 0);
        $id_fromQuery   = $params->fromQuery('id', 0);
        $id_fromSubForm = $params->fromPost('job', 0);

        $id = empty($id_fromRoute)? (empty($id_fromQuery)?$id_fromSubForm:$id_fromQuery) : $id_fromRoute;

        if (empty($id) && $allowDraft) {
            $this->acl->__invoke('Jobs/Manage', 'new');
            $user = $this->auth->getUser();
            /** @var \Jobs\Entity\Job $job */
            $job = $jobRepository->findDraft($user);
            if (empty($job)) {
                $job = $jobRepository->create();
                $job->setIsDraft(true);
                $job->setUser($user);
                $this->repositoryService->store($job);
            }
            return $job;
        }

        $job = $jobRepository->find($id);
        if (!$job) {
            throw new \RuntimeException('No job found with id "' . $id . '"');
        }
        return $job;
    }
}