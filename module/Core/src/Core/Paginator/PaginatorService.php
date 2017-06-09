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
    
    public function __construct(ContainerInterface $container, ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->container = $container;
    }
	
	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
    /**
     * check class
     *
     * @param mixed $plugin
     * @return bool|void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Paginator) {
            return true;
        }
        return false;
    }
}
