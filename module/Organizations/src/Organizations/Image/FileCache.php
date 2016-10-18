<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\Image;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\Http\Response;
use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Zend\Stdlib\ErrorHandler;

/**
 * This class provides caching of organization images to file system using event listeners
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class FileCache implements EventSubscriber
{
    
    /**
     * @var string
     */
    protected $filePath;
    
    /**
     * @var string
     */
    protected $uriPath;
    
    /**
     * @var array
     */
    protected $delete = [];
    
    /**
     * @param string $filePath
     * @param string $uriPath
     */
    public function __construct($filePath, $uriPath)
    {
        $this->filePath = $filePath;
        $this->uriPath = $uriPath;
    }

    /**
     * @param OrganizationImage $image
     * @return string
     */
    public function getUri(OrganizationImage $image)
    {
        return sprintf('%s/%s', $this->uriPath, $this->getImageSubPath($image));
    }
    
    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postFlush
        ];
    }
    
    /**
     * Creates and injects the organization reference to an user entity.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $organization = $eventArgs->getDocument();
        
        // check for a organization instance
        if (! $organization instanceof Organization) {
            return;
        }
        
        // check if the image has been changed
        if (! $eventArgs->hasChangedField('image')) {
            return;
        }
        
        $image = $eventArgs->getOldValue('image');
        
        // check if any image existed
        if (! $image instanceof OrganizationImage) {
            return;
        }
        
        // mark image for deletion
        $this->delete[] = $image;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        // delete images from file system
        foreach ($this->delete as $image) {
            @unlink($this->getImagePath($image));
        }
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
        
        // try match uri pattern
        $uri = $event->getRequest()->getRequestUri();
        $pattern = '#^' . preg_quote($this->uriPath, '#') . '/[0-9a-z]/[0-9a-z]/([0-9a-z]+)\.[a-zA-Z]{3,4}$#';
        $matches = [];
        preg_match($pattern, $uri, $matches);
        
        if (! isset($matches[1])) {
            // uri does not match organization image path
            return;
        }
        
        // try get image
        $id = $matches[1];
        $serviceManager = $event->getApplication()->getServiceManager();
        $repository = $serviceManager->get('repositories')->get('Organizations/OrganizationImage');
        $image = $repository->find($id);
        
        if (! $image) {
            // abort if image does not exist
            return;
        }
        
        $resource = $image->getResource();
        $path = $this->getImagePath($image);
        
        // create directory(ies)
        $this->createDirectoryRecursively(dirname($path));
        
        // store image
        file_put_contents($path, $resource);
        rewind($resource);
        
        // return image in response as a stream
        $headers = new Headers();
        $headers->addHeaders([
            'Content-Type' => $image->getType(),
            'Content-Length' => $image->getLength()
        ]);
        $response = new Stream();
        $response->setStream($resource);
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setStreamName($image->getName());
        $response->setHeaders($headers);
        $event->setResponse($response);
    }
    
    /**
     * @param OrganizationImage $image
     * @return string
     */
    protected function getImagePath(OrganizationImage $image)
    {
        return sprintf('%s/%s', $this->filePath, $this->getImageSubPath($image));
    }
    
    /**
     * @param OrganizationImage $image
     * @return string
     */
    protected function getImageSubPath(OrganizationImage $image)
    {
        $id = $image->getId();
        $firstLevel = substr($id, -1);
        $secondLevel = substr($id, -2, 1);
        
        return sprintf('%s/%s/%s.%s', $firstLevel, $secondLevel, $id, pathinfo($image->getName(), PATHINFO_EXTENSION));
    }

    /**
     * @param string $dir
     */
    protected function createDirectoryRecursively($dir)
    {
        $dir = rtrim($dir, '/\\');
        
        if (! is_dir($dir)) {
            $this->createDirectoryRecursively(dirname($dir));
            
            $oldUmask = umask(0);
            
            ErrorHandler::start();
            $created = mkdir($dir, 0775);
            $error = ErrorHandler::stop();
            
            if (!$created) {
                throw new \RuntimeException(sprintf('unable to create directory "%s"', $dir), 0, $error);
            }
            
            umask($oldUmask);
        }
    }
}
