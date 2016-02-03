<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Geo controller */
namespace Geo\Controller;

use Geo\Options\ModuleOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Class IndexController
 * @package Geo\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * Used geo coder plugin. Copy Geo.options.local.php.dist in your autoload and configure it
     *
     * @var string $plugin
     */
    protected $plugin;

    /**
     * Used geo coder server. Copy Geo.options.local.php.dist in your autoload and configure it
     *
     * @var string $geoCoderUrl
     */
    protected $geoCoderUrl;

    public function __construct(ModuleOptions $options) {
        $this->plugin = $options->getPlugin();
        $this->geoCoderUrl = $options->getGeoCoderUrl();
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $query = $this->params()->fromQuery();

        switch($this->plugin){
            case 'photon':
                /* @var Plugin\Photon $geoApi */
                $geoApi = $this->getPluginManager()->get('geo/photon');
                break;
            case 'geo':
                /* @var Plugin\Geo $geoApi */
                $geoApi = $this->getPluginManager()->get('geo/geo');
                break;
            default:
                throw new \RuntimeException('Invalid geo coder plugin');
        }
        $result = array();
        if (!empty($query['q'])) {
            $result = $geoApi($query['q'], $this->geoCoderUrl, $this->params('lang','de'));
        }
        $viewModel = new JsonModel($result);
        return $viewModel;
    }
}