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


use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;


/**
 * Main Action Controller for Applications module.
 *
 * @method \Core\Controller\Plugin\CreatePaginator paginator()
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
        /* @var Request $request */
        $request = $this->getRequest();
        $params = $request->getQuery();
        $isRecruiter = $this->Acl()->isRole('recruiter');
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
