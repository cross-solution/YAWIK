<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\View\Helper;

use Core\View\Helper\Params;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Jobs\View\Helper\JobUrl;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;

/**
 * Factory for JobUrl view helper
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class JobUrlFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = $container->get('ViewHelperManager');
        $url       = $viewHelper->get('url');
        $params    = $viewHelper->get('params');
        $serverUrl = $viewHelper->get('serverUrl');
	
	    $helper    = new JobUrl();
        $helper->setUrlHelper($url)
               ->setParamsHelper($params)
               ->setServerUrlHelper($serverUrl);
        return $helper;
    }
}
