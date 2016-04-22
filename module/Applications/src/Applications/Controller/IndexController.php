<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controller */
namespace Applications\Controller;

use Auth\Entity\Info;
use Applications\Entity\Application;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Applications\Entity\Status;
use Applications\Entity\StatusInterface;

/**
 * Main Action Controller for Applications module.
 *
 * @method \Core\Controller\Plugin\Notification notification()
 * @method \Core\Controller\Plugin\CreatePaginator paginator()
 * @method \Auth\Controller\Plugin\Auth auth()
 * @method \Acl\Controller\Plugin\Acl acl()
 */
class IndexController extends AbstractActionController
{
    /**
     * Handles dashboard listings of applications
     *
     * @return array
     */
    public function dashboardAction()
    {
        $params = $this->getRequest()->getQuery();
        $isRecruiter = $this->acl()->isRole('recruiter');
        if ($isRecruiter) {
            $params->set('by', 'me');
        }

         //default sorting
        if (!isset($params['sort'])) {
            $params['sort']="-date";
        }
        $params->count = 5;
        $params->pageRange=5;

        $this->paginationParams()->setParams('Applications\Index', $params);

        $paginator = $this->paginator('Applications', $params->toArray());
     
        return array(
            'script' => 'applications/index/dashboard',
            'applications' => $paginator
        );
    }
}
