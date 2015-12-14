<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/** ActionController of Jobs */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as Session;
use Zend\View\Model\JsonModel;
use Auth\Entity\User;
use Jobs\Repository;
use Jobs\Form\ListFilter;
use Zend\View\Model\ViewModel;

/**
 * Handles the job listing for recruiters.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 *
 * @method \Auth\Controller\Plugin\Auth auth()
 * @method \Acl\Controller\Plugin\Acl acl()
 * @method \Core\Controller\Plugin\CreatePaginator paginator(string $repositoryName, array $defaultParams = array(), bool $usePostParams = false)
 */
class IndexController extends AbstractActionController
{

    /**
     * @var Repository\Job $jobRepository
     */
    private $jobRepository;

    /**
     * Formular for searching job postings
     *
     * @var ListFilter $searchForm
     */
    private $searchForm;

    /**
     * attaches further Listeners for generating / processing the output
     *
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);

        return $this;
    }

    /**
     * List jobs.
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->listJobs();
    }

    public function listOpenJobsAction()
    {
        return $this->listJobs(true);
    }

    /**
     * @param bool $showPendingJobs
     *
     * @return ViewModel
     */
    protected function listJobs($showPendingJobs = false)
    {
        /* @var $request \Zend\Http\Request */
        $request     = $this->getRequest();
        $params      = $request->getQuery();
        $jsonFormat  = 'json' == $params->get('format');
        $isRecruiter = $this->acl()->isRole(User::ROLE_RECRUITER);

        if (!$jsonFormat && !$request->isXmlHttpRequest()) {
            $session       = new Session('Jobs\Index');
            $sessionKey    = $this->auth()->isLoggedIn() ? 'userParams' : 'guestParams';
            $sessionParams = $session[$sessionKey];

            if ($sessionParams) {
                foreach ($sessionParams as $key => $value) {
                    $params->set($key, $params->get($key, $value));
                }
            } elseif ($isRecruiter) {
                $params->set('by', 'me');
            }
            /* @var $filterForm \Jobs\Form\ListFilter */
            $session[$sessionKey] = $params->toArray();

            if (!$showPendingJobs) {
                $filterForm = $this->getServiceLocator()
                    ->get('forms')
                    ->get('Jobs/ListFilter', $isRecruiter);
                $filterForm->bind($params);
            }
        }

        if (!isset($params['sort'])) {
            $params['sort'] = '-date';
        }

        if ($showPendingJobs) {
            $paginator = $this->paginatorservice('Jobs/Admin', $params);
        } else {
            $paginator = $this->paginatorservice('Jobs/Job', $params);
        }

        $return = array(
            'by'   => $params->get('by', 'all'),
            'jobs' => $paginator,
            'showPendingJobs' => $showPendingJobs,
        );
        if (isset($filterForm)) {
            $return['filterForm'] = $filterForm;
        }

        $model = new ViewModel();
        $model->setVariables($return);
        $model->setTemplate('jobs/index/index');
        return $model;
    }

    /**
     * Handles the dashboard widget for the jobs module.
     *
     * @return array
     */
    public function dashboardAction()
    {
        /* @var $request \Zend\Http\Request */
        $services    = $this->getServiceLocator();
        $request     = $this->getRequest();
        $params      = $request->getQuery();
        $isRecruiter = $this->acl()->isRole(User::ROLE_RECRUITER);

        if ($isRecruiter) {
            $params->set('by', 'me');
        }

        $myJobs    = $services->get('repositories')->get('Jobs/Job');
        $paginator = $this->paginator('Jobs/Job');

        return array(
            'script' => 'jobs/index/dashboard',
            'type'   => $this->params('type'),
            'myJobs' => $myJobs,
            'jobs'   => $paginator
        );
    }

    /**
     * Action hook for Job search bar in list filter.
     *
     * @return JsonModel
     */
    public function typeaheadAction()
    {
        /* @var $repository \Jobs\Repository\Job */
        $query      = $this->params()->fromQuery('q', '*');
        $repository = $this->getServiceLocator()
                           ->get('repositories')
                           ->get('Jobs/Job');

        $return = array();
        foreach ($repository->getTypeaheadResults($query, $this->auth('id')) as $id => $item) {
            $return[] = array(
                'id'      => $id,
                'title'   => $item['title'],
                'applyId' => $item['applyId'],
            );
        }

        return new JsonModel($return);
    }
}
