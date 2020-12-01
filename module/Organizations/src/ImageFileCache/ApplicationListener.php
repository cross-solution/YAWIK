<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */
namespace Organizations\ImageFileCache;

use Core\Service\FileManager;
use Organizations\Entity\OrganizationImage;
use Organizations\ImageFileCache\Manager as CacheManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Application;
use Laminas\Http\Response;
use Laminas\Http\Headers;
use Laminas\Http\Response\Stream;
use Organizations\Repository\OrganizationImage as Repository;

/**
 * Image file cache application listener
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.28
 */
class ApplicationListener
{

    /**
     * @var CacheManager
     */
    protected CacheManager $cacheManager;

    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    public function __construct(
        CacheManager $cacheManager,
        FileManager $fileManager
    )
    {
        $this->cacheManager = $cacheManager;
        $this->fileManager = $fileManager;
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
        $fileManager = $this->fileManager;
        $cacheManager = $this->cacheManager;

        // try get image ID from URI
        $id = $cacheManager->matchUri($uri);

        if (!$id) {
            // abort if URI does not match
            return;
        }

        // try get image from repository
        /* @var OrganizationImage $image */
        $image = $fileManager->findByID(OrganizationImage::class, $id);

        if (! $image) {
            // abort if image does not exist
            return;
        }

        // store image
        $metadata = $image->getMetadata();
        $contents = $fileManager->getContents($image);
        $cacheManager->store($image, $contents);

        // return image in response as a stream
        $headers = new Headers();
        $headers->addHeaders([
            'Content-Type' => $metadata->getContentType(),
            'Content-Length' => $image->getLength()
        ]);
        $response = new Stream();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setStream($fileManager->getStream($image));
        $response->setStreamName($image->getName());
        $response->setHeaders($headers);
        $event->setResponse($response);
    }
}
