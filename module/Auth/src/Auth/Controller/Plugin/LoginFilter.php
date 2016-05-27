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

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Auth\Filter\LoginFilter as LoginFilterService;
use Zend\Mvc\Controller\PluginManager as ControllerManager;

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
     * @param ControllerManager $controllerManager
     * @return \Auth\Controller\Plugin\LoginFilter
     */
    public static function factory(ControllerManager $controllerManager)
    {
        return new static($controllerManager->getServiceLocator()->get('Auth/LoginFilter'));
    }
}
