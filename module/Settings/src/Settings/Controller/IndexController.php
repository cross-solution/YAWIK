<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Settings controller */
namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use Applications\Form\Application as ApplicationForm;
//use Applications\Model\Application as ApplicationModel;
//use Applications\Form\ApplicationHydrator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;

/**
 * Main Action Controller for Applications module.
 *
 */
class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }
}