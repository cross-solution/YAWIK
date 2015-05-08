<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 */
class ApplyUrlFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $helper    = new ApplyUrl();
        $url       = $serviceLocator->get('url');
        $translate = $serviceLocator->get('translate');
        $params    = $serviceLocator->get('params');
        $serverUrl = $serviceLocator->get('serverUrl');
        $helper->setUrlHelper($url)
            ->setTranslateHelper($translate)
            ->setParamsHelper($params)
            ->setServerUrlHelper($serverUrl);
        return $helper;
    }

}