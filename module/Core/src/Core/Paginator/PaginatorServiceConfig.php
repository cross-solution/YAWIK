<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Paginator;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

/**
 * Class PaginatorServiceConfig
 * @package Core\Paginator
 */
class PaginatorServiceConfig extends Config
{

    /**
     * @param ServiceManager $serviceManager
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        parent::configureServiceManager($serviceManager);
    }
}
