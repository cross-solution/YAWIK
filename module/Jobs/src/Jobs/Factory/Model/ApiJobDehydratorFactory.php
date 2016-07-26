<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Model;

use Jobs\Model\ApiJobDehydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for ApiJobDehydrator
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class ApiJobDehydratorFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApiJobDehydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $viewManager = $serviceLocator->get('ViewHelperManager');
        $urlHelper = $viewManager->get('url');
        $apiJobDehydrator = new ApiJobDehydrator();
        $apiJobDehydrator->setUrl($urlHelper);
        return $apiJobDehydrator;
    }
}
