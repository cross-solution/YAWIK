<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @license   MIT
 */

namespace Jobs\Controller\Plugin;

use Jobs\Entity\StatusInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Repository\RepositoryService;
use Auth\AuthenticationService;
use Zend\Mvc\Controller\Plugin\Params;
use Acl\Controller\Plugin\Acl;
use Core\Entity\Exception\NotFoundException;

/**
 * Class InitializeJob
 *
 * @package Jobs\Controller\Plugin
 */
class InitializeJob extends AbstractPlugin
{

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

    public function __construct(RepositoryService $repositoryService, AuthenticationService $auth, Acl $acl)
    {
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
     * @throws NotFoundException
     */
    public function get(Params $params, $allowDraft = false, $getSnapshot = false)
    {
        /* @var \Jobs\Repository\Job $jobRepository */
        $jobRepository  = $this->repositoryService->get('Jobs/Job');
        $idFromRoute   = $params('id', 0);
        $idFromQuery   = $params->fromQuery('id', 0);
        $idFromSubForm = $params->fromPost('job', 0);

        $id = empty($idFromRoute)? (empty($idFromQuery)?$idFromSubForm:$idFromQuery) : $idFromRoute;
        $snapshotId = $params->fromPost('snapshot') ?: ($params->fromQuery('snapshot') ?: null);

        if (empty($id) && empty($snapshotId) && $allowDraft) {
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

        if ($snapshotId) {
            $snapshotRepo = $this->repositoryService->get('Jobs/JobSnapshot');
            $job = $snapshotRepo->find($snapshotId);
        } else {
            /* @var \Jobs\Entity\Job $job */
            $job = $jobRepository->find($id);
            if ($job && $getSnapshot && !$job->isDraft() && $job->getStatus()->getName() != \Jobs\Entity\StatusInterface::CREATED) {
                $snapshotRepo = $this->repositoryService->get('Jobs/JobSnapshot');
                $snapshot = $snapshotRepo->findLatest($job->getId(), /*isDraft*/ true);

                $job = $snapshot ?: $snapshotRepo->create($job, true);
            }
        }

        if (!$job || $job->isDeleted()) {
            throw new NotFoundException($id);
        }

        return $job;
    }
}
