<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Jobs */
namespace Jobs\Controller;

use Core\Form\SearchForm;
use Jobs\Form\ListFilter;
use Jobs\Listener\Events\JobEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as Session;
use Jobs\Repository;
use Zend\View\Model\ViewModel;

/**
 * @method \Auth\Controller\Plugin\Auth auth()
 * @method \Core\Controller\Plugin\CreatePaginatorService paginatorService()
 *
 * Controller for jobboard actions
 */
class JobboardController extends AbstractActionController
{
    /**
     * @var Repository\Job $jobRepository
     */
    private $jobRepository;

    /**
     * @var array
     */
    private $options = [
        'count' => 10
    ];

    /**
     * Construct the jobboard controller
     *
     * @param Repository\Job $jobRepository
     */
    public function __construct(Repository\Job $jobRepository, $options)
    {
        $this->jobRepository = $jobRepository;
        $this->options = $options;
    }
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator = $this->serviceLocator;
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * List jobs
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        /* @todo: move this into a listener.
         *
         * The following lines allow to override get param[q] with the
         * param from route. This feature is needed for a landing-page feature, where
         * human readable urls like http://yawik.org/demo/de/jobs/sales.html
         *
         * move the Logic into a Listener, which can be activated, if needed
         */
        $request = $this->getRequest();
        $getParams = $request->getQuery();
        $routeParams = $this->params()->fromRoute();
        if (isset($routeParams['q']) && !isset($getParams['q'])){
            $getParams['q']=$routeParams['q'];
        }

        $job = $this->serviceLocator->get('repositories')->get('Jobs')->find('561b86e3d3b93f356d732bcf');
        $events = $this->serviceLocator->get('Jobs/Events');
        $jobEvent       = $this->serviceLocator->get('Jobs/Event');
        $jobEvent->setJobEntity($job);
        $jobEvent->addPortal('stackoverflow');

        $events->trigger(JobEvent::EVENT_JOB_ACCEPTED, $jobEvent);
        $this->getResponse()->setContent('voila!');

        return $this->response;

        $result = $this->pagination([
                'params' => ['Jobs_Board', [
                    'q',
                    'count' => $this->options['count'],
                    'page' => 1,
                    'l',
                    'd' => 10]
                ],
                'form' => ['as' => 'filterForm', 'Jobs/JobboardSearch'],
                'paginator' => ['as' => 'jobs', 'Jobs/Board']
            ]);

        $params['by'] = "guest";

        $organizationImageCache = $this->serviceLocator->get('Organizations\ImageFileCache\Manager');

        $result['organizationImageCache'] = $organizationImageCache;

        return $result;
    }
}
