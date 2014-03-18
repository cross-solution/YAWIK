<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Geo controller */
namespace Geo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

/**
 * 
 */
class IndexController extends AbstractActionController
{
    /**
     * 
     * @todo document
     */
    public function indexAction() {
        $query = $this->params()->fromQuery();
        $geoApi = $this->getPluginManager()->get('geo/geo');
        $result = array();
        if (!empty($query['q'])) {
            $result = $geoApi($query['q']);
        }
        $viewModel = new JsonModel($result);
        return $viewModel;
    }
}