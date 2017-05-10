<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SessionParams.php */
namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;
use Core\Repository\RepositoryInterface;
use Zend\Stdlib\Parameters;

/**
 * Manages pagination parameters in a session container.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class PaginationParams extends AbstractPlugin
{
    
    /**
     * Invoke object as function.
     *
     * if <i>$namespace</i> is given, proxies to {@link getParams}, if <i>$defaults</i>
     * is an array and not callable or proxises to {@link getList} in the other case.
     *
     * if called without arguments, returns itself.
     *
     * @param string|null $namespace
     * @param array|RepositoryInterface $defaults
     * @return \Core\Controller\Plugin\PaginationParams|Parameters|PaginationList
     */
    public function __invoke($namespace = null, $defaults = array('page' => 1), $params = null)
    {
        if (null === $namespace) {
            return $this;
        }
        
        if (is_array($defaults) && !is_callable($defaults)) {
            return $this->getParams($namespace, $defaults, $params);
        }
        
        if ($defaults instanceof RepositoryInterface || is_callable($defaults)) {
            return $this->getList($namespace, $defaults);
        }
    }
    
    /**
     * Sets pagination params and stores them in the session.
     *
     * @param String $namespace
     * @param array|Parameters $params
     * @return \Core\Controller\Plugin\PaginationParams fluent interface
     */
    public function setParams($namespace, $params)
    {
        $session = new Container($namespace);
        $session->params = $params;
        unset($session->list);
        return $this;
    }
    
    /**
     * Retrieves pagination params.
     *
     * Automatically merges parameters stored in the session according to specs
     * provided.
     *
     * @param string $namespace Session namespace
     * @param array $defaults
     *      1. [paramName] => [defaultValue],
     *         Set default value if paramName is not present in params
     *      2. [paramName]
     *         Store paramName if it is present, do nothing if not.
     * @param Parameters $params
     *
     * @return Parameters
     */
    public function getParams($namespace, $defaults, $params = null)
    {
        $session        = new Container($namespace);
        $sessionParams  = $session->params ?: array();
        $params         = $params ?: clone $this->getController()->getRequest()->getQuery();
        
        if ($params->get('clear')) {
            $sessionParams = array();
            unset($params['clear']);
        }
        
        $changed = false;
        foreach ($defaults as $key => $default) {
            if (is_numeric($key)) {
                $key = $default;
                $default = null;
            }
            $value = $params->get($key);
            if (null === $value) {
                if (isset($sessionParams[$key])) {
                    $params->set($key, $sessionParams[$key]);
                } elseif (null !== $default) {
                    $params->set($key, $default);
                    $sessionParams[$key] = $default;
                    $changed = true;
                }
            } else {
                if (!isset($sessionParams[$key]) || $sessionParams[$key] != $value) {
                    $changed = true;
                    $sessionParams[$key] = $value;
                }
            }
        }
        
        if ($changed) {
            unset($session->list);
            $session->params = $sessionParams;
        }
        
        return $params;
    }
    
    /**
     * Gets the list of ids.
     *
     * If no list is stored in the session, it will try to create one
     * using the given callback, which is either a RepositoryInterface or a
     * callable. In the first case, a method called "getPaginationList" will get called.
     * The stored parameters (or an empty array, if nothing is stored) will be passed.
     *
     * @param string $namespace
     * @param RepositoryInterface|callable $callback
     *
     * @return array
     */
    public function getList($namespace, $callback)
    {
        $session = new Container($namespace);
        $params  = $session->params?:array();
        if (!$session->list) {
            $session->list = is_array($callback)
            ? call_user_func($callback, $session->params)
            : $callback->getPaginationList($session->params);
        }
        return $session->list;
    }
        
    /**
     * Get previous and next id from the list.
     *
     * @param string $namespace
     * @param RepositoryInterface|callable $callback
     * @param string $id Current application id
     * @return array
     *          first entry: previous id or null
     *          second entry: next id or null
     */
    public function getNeighbours($namespace, $callback, $id)
    {
        $list = $this->getList($namespace, $callback);
        $list->setCurrent($id);
        return [
            $list->getPrevious(),
            $list->getNext()
        ];
    }
}
