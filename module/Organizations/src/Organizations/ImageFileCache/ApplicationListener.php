<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\ImageFileCache;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\Http\Response;
use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Organizations\Repository\OrganizationImage as Repository;

/**
 * Image file cache application listener
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ApplicationListener
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Repository
     */
    protected $repository;
    
    /**
     * @param Manager $manager
     * @param Repository $repository
     */
    public function __construct(Manager $manager, Repository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatchError(MvcEvent $event)
    {
        if (Application::ERROR_ROUTER_NO_MATCH != $event->getError()) {
            // ignore other than 'no route' errors
            return;
        }

        // get URI stripped of a base URL
        $request = $event->getRequest();
        $uri = str_replace($request->getBaseUrl(), '', $request->getRequestUri());
        
        // try get image ID from URI
        $id = $this->manager->matchUri($uri);

        if (!$id) {
            // abort if URI does not match
            return;
        }

        // try get image from repository
        $image = $this->repository->find($id);

        if (! $image) {
            // abort if image does not exist
            return;
        }

        // store image
        $this->manager->store($image);

        // return image in response as a stream
        $headers = new Headers();
        $headers->addHeaders([
            'Content-Type' => $image->getType(),
            'Content-Length' => $image->getLength()
        ]);
        $response = new Stream();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setStream($image->getResource());
        $response->setStreamName($image->getName());
        $response->setHeaders($headers);
        $event->setResponse($response);
    }
}
