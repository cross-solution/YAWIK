<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\MvcEvent;

/**
 * @todo Write factory, configuration must be possible
 * @author mathias
 *
 */
class Params extends AbstractHelper
{
    
    protected $event;
    
    public function __construct(MvcEvent $e) {
        $this->event = $e;
    }
    
    /**
     * Grabs a param from route match by default.
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
        return $value ? $value : $this->event->getParam($param, $default);
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
            return $this->event>getRequest()->getPost($param, $default)->toArray();
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
}