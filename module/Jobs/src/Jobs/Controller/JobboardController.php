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
     * Formular for searching job postings
     *
     * @var ListFilter $searchForm
     */
    private $searchForm;

    /**
     * Construct the jobboard controller
     *
     * @param Repository\Job $jobRepository
     */
    public function __construct(Repository\Job $jobRepository)
    {
        $this->jobRepository = $jobRepository;
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

        $result = $this->pagination([
                'params' => ['Jobs_Board', ['q', 'page' => 1, 'l', 'd']],
                'form' => ['as' => 'filterForm', 'Jobs/JobboardSearch'],
                'paginator' => ['as' => 'jobs', 'Jobs/Board']
            ]);

        $params['by'] = "guest";

        $organizationImageCache = $this->serviceLocator->get('Organizations\ImageFileCache\Manager');

        $result['organizationImageCache'] = $organizationImageCache;

        return $result;
    }
}
