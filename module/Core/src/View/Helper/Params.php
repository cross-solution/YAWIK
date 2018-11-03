<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helpers */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\MvcEvent;

/**
 * This helper exposes request params to view scripts.
 *
 * <code>
 *      // Gets route match param or event param or null.
 *      $param = $this->params('routeoreventparam');
 *
 *      // Gets route match param or event param or returns the default
 *      $param = $this->param('routeoreventparam', 'defaultValue');
 *
 *      // access helper methods:
 *      $this->params()->fromRoute('routeParam');
 *      $this->params()->fromQuery('queryParam', 'default');
 *
 *      // All methods:
 *      // - fromEvent() : Gets event parameters.
 *      // - fromFiles() : Gets uploaded files.
 *      // - fromHeader(): Gets request headers.
 *      // - fromPost()  : Gets post parameters.
 *      // - fromQuery() : Gets query parameters (e.g. ?param=value&param_two=VAL)
 *      // - fromRoute() : Gets route match parameters
 * </code>
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Params extends AbstractHelper
{

    /**
     * The Mvc Event
     * @var MvcEvent
     */
    protected $event;
    
    /**
     * Creates an instance.
     *
     * @param MvcEvent $e
     */
    public function __construct(MvcEvent $e)
    {
        $this->event = $e;
    }
    
    /**
     * Grabs a param from route match or event by default.
     *
     * If <b>$param</b> is <i>NULL</i> returns itself.
     *
     * Tries to grab a param from route match first.
     * If route match does not have a param called <b>$param</b>,
     * it tries to grab this param from the event.
     *
     * If the event does not have the param, it returns <b>$default</b>
     *
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($param = null, $default = null)
    {
        if ($param === null) {
            return $this;
        }

        $value = $this->fromRoute($param, null);
        return $value ? $value : $this->fromEvent($param, $default);
    }
    
    /**
     * Return all files or a single file.
     *
     * @param  string $name File name to retrieve, or null to get all.
     * @param  mixed $default Default value to use when the file is missing.
     * @return array|\ArrayAccess|null
     */
    public function fromFiles($name = null, $default = null)
    {
        if ($name === null) {
            return $this->event->getRequest()->getFiles($name, $default)->toArray();
        }
    
        return $this->event->getRequest()->getFiles($name, $default);
    }
    
    /**
     * Return all header parameters or a single header parameter.
     *
     * @param  string $header Header name to retrieve, or null to get all.
     * @param  mixed $default Default value to use when the requested header is missing.
     * @return null|\Zend\Http\Header\HeaderInterface
     */
    public function fromHeader($header = null, $default = null)
    {
        if ($header === null) {
            return $this->event->getRequest()->getHeaders($header, $default)->toArray();
        }
    
        return $this->event->getRequest()->getHeaders($header, $default);
    }
    
    /**
     * Return all post parameters or a single post parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $default Default value to use when the parameter is missing.
     * @return mixed
     */
    public function fromPost($param = null, $default = null)
    {
        if ($param === null) {
            return $this->event->getRequest()->getPost($param, $default)->toArray();
        }
    
        return $this->event->getRequest()->getPost($param, $default);
    }
    
    /**
     * Return all query parameters or a single query parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $default Default value to use when the parameter is missing.
     * @return mixed
     */
    public function fromQuery($param = null, $default = null)
    {
        if ($param === null) {
            return $this->event->getRequest()->getQuery($param, $default)->toArray();
        }
    
        return $this->event->getRequest()->getQuery($param, $default);
    }
    
    /**
     * Return all route parameters or a single route parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $default Default value to use when the parameter is missing.
     * @return mixed
     * @throws RuntimeException
     */
    public function fromRoute($param = null, $default = null)
    {
        if (!$this->event->getRouteMatch()) {
            return $default;
        }
        if ($param === null) {
            return $this->event->getRouteMatch()->getParams();
        }
        return $this->event->getRouteMatch()->getParam($param, $default);
    }
    
    /**
     * Return all event parameters or a single event parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $efault Default value to use when the parameter is missing.
     * @return mixed
     * @throws RuntimeException
     */
    public function fromEvent($param = null, $default = null)
    {
        if (null === $param) {
            return $this->event->getParams();
        }
        return $this->event->getParam($param, $default);
    }
}
