<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** IndexController of the Admin Module */
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * The Index Controller contains actions for handling static content.
 *
 */
class ConfigController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('admin/config/'. $this->params()->fromRoute('section'));

        $services = $this->getServiceLocator();
        $container = $services->get('forms')->get('Admin\Form\Config');

        $viewModel->setVariable('form' , $container);
        return $viewModel;
    }
}