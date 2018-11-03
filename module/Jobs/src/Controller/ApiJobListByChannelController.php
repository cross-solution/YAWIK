<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Export controller */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Export jobs as XML feed
 *
 * @method \Core\Controller\Plugin\CreatePaginator pagination()
 */
class ApiJobListByChannelController extends AbstractActionController
{

    /**
     * List Jobs
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function listAction()
    {
        $channel = $this->params()->fromRoute('channel', 'default');

        /* @var Paginator $paginator */
        $paginator = $this->paginator('Jobs/Job', ['channel' => $channel ]);

        $viewModel=new ViewModel();
        $viewModel->setVariables(
            [
                'jobs' => $paginator,
                'channel' => $channel
            ]
        );

        $viewModel->setTemplate('jobs/export/feed');

        return $viewModel;
    }
}
