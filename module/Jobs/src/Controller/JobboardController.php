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
use Core\Listener\DefaultListener;
use Jobs\Form\ListFilter;
use Jobs\Listener\Events\JobEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as Session;
use Jobs\Repository;
use Zend\View\Model\ViewModel;
use Organizations\ImageFileCache\Manager as ImageFileCacheManager;
use Zend\View\Model\JsonModel;

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

    private $defaultListener;

    private $imageFileCacheManager;

    /**
     * Construct the jobboard controller
     *
     * @param Repository\Job $jobRepository
     */
    public function __construct(
        DefaultListener $defaultListener,
        Repository\Job $jobRepository,
        ImageFileCacheManager $imageFileCacheManager,
        $options
    ) {
        $this->jobRepository = $jobRepository;
        $this->options = $options;
        $this->defaultListener = $defaultListener;
        $this->imageFileCacheManager = $imageFileCacheManager;
    }
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events          = $this->getEventManager();
        $this->defaultListener->attach($events);
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
        if (isset($routeParams['q']) && !isset($getParams['q'])) {
            $getParams['q']=$routeParams['q'];
        }

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

        $organizationImageCache = $this->imageFileCacheManager;

        $result['organizationImageCache'] = $organizationImageCache;

        $isJson = $this->params()->fromQuery('json', false);

        if ($isJson) {
            return $this->getJsonView($result);
        }

        return new ViewModel($result);
    }

    private function getJsonView($result)
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Access-Control-Allow-Origin', '*');

        $result = $this->processJsonRequest($result);

        return new JsonModel($result);
    }
}
