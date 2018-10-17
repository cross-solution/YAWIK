<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/**  */
namespace Core\Controller\Plugin;

use Core\EventManager\EventManager;
use Core\Listener\Events\CreatePaginatorEvent;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\PluginManager as ControllerManager;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Zend\Http\Request as HttpRequest;

/**
 * Creates a paginator from the paginator service.
 *
 * Passing in GET (or POST) request parameters as creation options to the paginator manager.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.30 - ZF3 compatibility
 */
class CreatePaginator extends AbstractPlugin
{
    const EVENT_CREATE_PAGINATOR = 'core.create_paginator';
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;
    
    /**
     * @var HttpRequest
     */
    protected $request;
    
    /**
     * CreatePaginator constructor.
     *
     * @param ContainerInterface $container
     * @param HttpRequest $request
     */
    public function __construct(ContainerInterface $container, HttpRequest $request)
    {
        $this->serviceManager = $container->get('ServiceManager');
        $this->request = $request;
    }
    
    /**
     * Creates a paginator from the paginator service.
     *
     * Uses query parameters from the request merged with $defaultParams as
     * creation options while retrieving the service.
     * Please note that a query parameter with the same name as a default parameter
     * overrides the default parameter.
     *
     *
     * @param string $paginatorName
     * @param array  $defaultParams
     * @param Parameters|bool   $params false: Use query parameters; true: use post parameters
     *
     * @return \Zend\Paginator\Paginator
     * @throws \InvalidArgumentException
     */
    public function __invoke($paginatorName, $defaultParams = array(), $params = false)
    {
        if (is_bool($defaultParams)) {
            $params = $defaultParams;
            $defaultParams = array();
        }

        if (!is_array($defaultParams) && !$defaultParams instanceof \Traversable) {
            throw new \InvalidArgumentException('$defaultParams must be an array or implement \Traversable');
        }

        /** @var \Core\Paginator\PaginatorService $paginators */
        $paginators = $this->serviceManager->get('Core/PaginatorService');

        if (!$params) {
            $params = $this->request->getQuery()->toArray();
        } elseif (true === $params) {
            $params = $this->request->getPost()->toArray();
        } elseif ($params instanceof \ArrayObject) {
            $params = $params->getArrayCopy();
        }

        // We allow \Traversable so we cannot simply merge.
        foreach ($defaultParams as $key => $val) {
            if (!isset($params[$key])) {
                $params[$key] = $val;
            }
        }

        /* try to create $paginator from event listener */
        /* @var \Core\EventManager\EventManager $events */
        /* @var \Zend\Paginator\Paginator $paginator */
        /* @var CreatePaginatorEvent $event */
        $events = $this->serviceManager->get('Core/CreatePaginator/Events');

        $event = $events->getEvent(CreatePaginatorEvent::EVENT_CREATE_PAGINATOR, $this, [
            'paginatorParams' => $params,
            'paginators' => $paginators,
            'paginatorName' => $paginatorName
        ]);
        $events->triggerEvent($event);

        $paginator = $event->getPaginator();

        if (!$paginator instanceof Paginator) {
            // no paginator created by listener, so let's create default paginator
            $paginator = $paginators->get($paginatorName, $params);
        }
        $paginator->setCurrentPageNumber(isset($params['page']) ? $params['page'] : 1)
                  ->setItemCountPerPage(isset($params['count']) ? $params['count'] : 10)
                  ->setPageRange(isset($params['range']) ? $params['range'] : 5);

        return $paginator;
    }
    
    /**
     * @param ControllerManager $controllerManager
     * @return CreatePaginator
     * @codeCoverageIgnore
     */
    public static function factory(ContainerInterface $container)
    {
        $request = $container->get('Request');
        
        if (!$request instanceof HttpRequest) {
            // use an empty HTTP request in a CLI environment
            $request = new HttpRequest();
        }
        
        return new static($container, $request);
    }
}
