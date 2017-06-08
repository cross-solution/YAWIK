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
        $client = $this->serviceLocator->get('Geo/Client');
        $result = $client->query($this->params()->fromQuery('q'), ['lang' => $this->params('lang')]);
        return new JsonModel(['items' => $result]);
    }
}