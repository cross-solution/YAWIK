<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Cv\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.
 *
 */
class IndexController extends AbstractActionController
{

    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * Home site
     *
     */
    public function indexAction()
    {
        $paginator = $this->paginator('Cv');
            
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        
        if ($jsonFormat) {
            $viewModel = new JsonModel();
            //$items = iterator_to_array($paginator);
        
            $viewModel->setVariables(
                array(
                'items' => $this->getServiceLocator()->get('builders')->get('JsonCv')
                                ->unbuildCollection($paginator->getCurrentItems()),
                'count' => $paginator->getTotalItemCount()
                )
            );
            return $viewModel;
        
        }
        
        return array(
            'resumes' => $paginator,
            'sort' => $this->params()->fromQuery('sort', 'none')
        );
    }
}
