<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** Export controller */
namespace Jobs\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

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
     * @return \Laminas\Http\Response|ViewModel
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
