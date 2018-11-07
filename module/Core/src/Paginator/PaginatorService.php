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

use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Paginator\Paginator;

/**
 * Class PaginatorService
 * @package Core\Paginator
 */
class PaginatorService extends AbstractPluginManager
{
    
    /**
     * @var bool
     */
    protected $shareByDefault = false;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    public function __construct(
        ContainerInterface $container,
        array $configuration = array()
    ) {
        parent::__construct($container, $configuration);
        $this->container = $container;
    }
    
    /**
     * @return ContainerInterface
     */
    public function getRepositories()
    {
        return $this->container;
    }
    
    /**
     * check class
     *
     * @param mixed $plugin
     * @return bool|void
     */
    public function validate($plugin)
    {
        if ($plugin instanceof Paginator) {
            return true;
        }
        return false;
    }
}
