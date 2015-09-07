<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Geo controller */
namespace Geo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Class IndexController
 * @package Geo\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     *
     * @todo document
     */
    public function indexAction()
    {
        $query = $this->params()->fromQuery();

        $geoApi = $this->getPluginManager()->get('geo/'.$this->params('plugin'));
        $result = array();
        if (!empty($query['q'])) {
            $result = $geoApi($query['q']);
        }
        $viewModel = new JsonModel($result);
        return $viewModel;
    }
}
