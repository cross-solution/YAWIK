<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileSender.php */
namespace Core\Controller\Plugin;

use Core\Service\FileManager;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class FileSender
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @package Core\Controller\Plugin
 */
class FileSender extends AbstractPlugin
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * FileSender constructor.
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }
    
    public function __invoke($repositoryName, $fileId)
    {
        return $this->sendFile($repositoryName, $fileId);
    }
    
    public function sendFile(string $entityClass, $fileId)
    {
        $fileManager = $this->fileManager;
        $file        = $fileManager->findByID($entityClass, $fileId);
        $response    = $this->getController()->getResponse();
        
        if (is_null($file)) {
            $response->setStatusCode(404);
            return null;
        }

        $metadata = $file->getMetadata();
        $response->getHeaders()->addHeaderline('Content-Type', $metadata->getContentType())
                               ->addHeaderline('Content-Length', $file->getLength());
        $response->sendHeaders();
        
        $resource = $fileManager->getStream($file);
        while (!feof($resource)) {
            echo fread($resource, 1024);
        }
        //@TODO: [ZF3] check if removing "exit;" is safe
        //exit;
        return $response;
    }
    
    /**
     * @param ContainerInterface $container
     *
     * @return static
     */
    public static function factory(ContainerInterface $container)
    {
        $fileManager = $container->get(FileManager::class);
        return new static($fileManager);
    }
}
