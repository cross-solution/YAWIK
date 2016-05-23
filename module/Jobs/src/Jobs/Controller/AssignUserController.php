<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Controller;

use Jobs\Repository\Job as JobsRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * ${CARET}
 *
 * @method \Acl\Controller\Plugin\Acl acl()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AssignUserController extends AbstractActionController
{

    protected $repository;

    /**
     * The job entity
     *
     * @var \Jobs\Entity\Job
     */
    protected $job;

    public function __construct(JobsRepository $repository)
    {
        $this->repository = $repository;
    }


    public function indexAction()
    {
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest()) {
            throw new \RuntimeException('This action must be called via ajax request ONLY!');
        }

        $this->common();
        return $request->isPost()
               ? $this->save()
               : $this->render();
    }

    protected function common()
    {
        $id = $this->params()->fromQuery('id');
        $job = $this->repository->find($id);

        if (!$job) {
            throw new \RuntimeException('No job found with id "' . $id . '"');
        }

        $this->acl($job, 'edit');
        $this->job = $job;
    }

    protected function render()
    {
        $organization = $this->job->getOrganization();
        if ($organization->isHiringOrganization()) {
            $organization = $organization->getParent();
        }

        $users = array($organization->getUser());
        foreach ($organization->getEmployees() as $emp) {
            /* @var $emp \Organizations\Entity\Employee */
            $users[] = $emp->getUser();
        }

        $model = new ViewModel();
        $model->setVariables(
            array(
            'currentUser' => $this->job->getUser(),
            'users' => $users,
            'organization' => $organization,
            'job' => $this->job,
            )
        );
        $model->setTemplate('jobs/assign-user');
        return $model;
    }

    protected function save()
    {
        $userId = $this->params()->fromPost('userId');

        $org = $this->job->getOrganization();
        if ($org->isHiringOrganization()) {
            $org = $org->getParent();
        }

        /*
         * Maybe we should inject the user repository also, to prevent this
         * rather expensive loop. On the other hand... how often will someone change the job user?
         */
        if ($org->getUser()->getId() == $userId) {
            $this->job->setUser($org->getUser());
        } else {
            /* @var \Organizations\Entity\Employee  $emp */
            foreach ($org->getEmployees() as $emp) {
                $user = $emp->getUser();
                if ($user->getId() == $userId) {
                    $this->job->setUser($user);
                }
            }
        }

        $model = new JsonModel(array('success' => true));

        return $model;
    }
}
