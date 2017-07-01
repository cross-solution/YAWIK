<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Auth\Filter\LoginFilter as LoginFilterService;

/**
 * Class LoginFilter
 * @package Auth\Controller\Plugin
 */
class LoginFilter extends AbstractPlugin
{

    /**
     * @var LoginFilterService
     */
    protected $loginFilter;
    
    /**
     * @param LoginFilterService $loginFilter
     */
    public function __construct(LoginFilterService $loginFilter)
    {
        $this->loginFilter = $loginFilter;;
    }

    /**
     * @param string $name
     * @return string
     */
    public function __invoke($name)
    {
        return $this->loginFilter->filter($name);
    }
	
	
	/**
	 * @param ContainerInterface $container
	 *
	 * @return static
	 */
    public static function factory(ContainerInterface $container)
    {
        return new static($container->get('Auth/LoginFilter'));
    }
}
