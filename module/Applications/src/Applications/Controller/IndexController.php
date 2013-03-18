<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Applications controller */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Applications\Form\Application as ApplicationForm;

/**
 * Main Action Controller for Applications module.
 *
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Main apply site
     *
     */
    public function indexAction()
    { 
//         $view = new ViewModel();
//         $view->setTerminal(true);
//         return $view;
        $this->layout('layout/apply');
        
        return array(
            'job' => (object) array(
                'title' => 'Testjob'
            ),
            'form' => new ApplicationForm()
        );
        
    }
    
    public function submitAction()
    {
        
    }
    
    
}
