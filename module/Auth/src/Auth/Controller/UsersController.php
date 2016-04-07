<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Auth\AuthenticationService;
use Auth\Repository\User;
use Auth\Service\Exception;
use Auth\Options\ModuleOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * List registered users
 */
class UsersController extends AbstractActionController
{

    /**
     * Login with username and password
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function listAction()
    {
        /* @var \Zend\Http\Request $request */
        $request          = $this->getRequest();
        $params           = $request->getQuery();

        $paginator = $this->paginator('Auth/User', $params);
        $form = $this->getServiceLocator()->get('forms')
                ->get('Core/TextSearch', [
                                           'placeholder' => /*@translate*/ 'Type name, email address, role, or login name',
                                           'button_element' => 'text'
                                       ]);

        $return = array(
            'by' => $params['by'],
            'users' => $paginator,
            'form' => $form,
        );

        return $return;
    }
}
