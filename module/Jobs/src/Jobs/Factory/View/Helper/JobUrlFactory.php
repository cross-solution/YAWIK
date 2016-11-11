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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Jobs\View\Helper\JobUrl;

/**
 * Factory for JobUrl view helper
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class JobUrlFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $helper    = new JobUrl();
        $url       = $serviceLocator->get('url');
        $params    = $serviceLocator->get('params');
        $serverUrl = $serviceLocator->get('serverUrl');
        $helper->setUrlHelper($url)
            ->setParamsHelper($params)
            ->setServerUrlHelper($serverUrl);
        return $helper;
    }
}
