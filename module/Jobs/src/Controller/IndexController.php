<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
 * @method \Core\Controller\Plugin\CreatePaginator paginator()
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
     * Construct the index controller
     *
     * @param Repository\Job $jobRepository
     * @param ListFilter $searchForm
     */
    public function __construct(Repository\Job $jobRepository, ListFilter $searchForm)
    {
        $this->jobRepository = $jobRepository;
        $this->searchForm = $searchForm;
    }

    /**
     * List job postings
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        /* @var $request \Zend\Http\Request */
        $request     = $this->getRequest();
        $queryParams = $request->getQuery();
        $params      = $queryParams->get('params', []);
        $jsonFormat  = 'json' == $queryParams->get('format');
        $isRecruiter = $this->acl()->isRole(User::ROLE_RECRUITER);

        if (!$jsonFormat && !$request->isXmlHttpRequest()) {
            $session       = new Session('Jobs\Index');
            $sessionKey    = $this->auth()->isLoggedIn() ? 'userParams' : 'guestParams';
            $sessionParams = $session[$sessionKey];

            if ($sessionParams) {
                $params = array_merge($sessionParams, $params);
            } elseif ($isRecruiter) {
                $params['by'] = 'me';
            }

            $session[$sessionKey] = $params;
            $queryParams->set('params', $params);

            $this->searchForm->bind($queryParams);
        }

        if (!isset($params['sort'])) {
            $params['sort'] = '-date';
        }

        $paginator = $this->paginator('Jobs/Job', $params);

        $return = [
            'by'   => $queryParams->get('by', 'all'),
            'jobs' => $paginator,
        ];
        if (isset($this->searchForm)) {
            $return['filterForm'] = $this->searchForm;
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
        $request     = $this->getRequest();
        $params      = $request->getQuery();
        $isRecruiter = $this->Acl()->isRole(User::ROLE_RECRUITER);

        if ($isRecruiter) {
            $params->set('by', 'me');
        }

        $paginator = $this->paginator('Jobs/Job');

        return [
            'script' => 'jobs/index/dashboard',
            'type'   => $this->params('type'),
            'myJobs' => $this->jobRepository,
            'jobs'   => $paginator
        ];
    }
}
