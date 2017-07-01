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

use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

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
	 * @var RepositoryService
	 */
    private $repositories;
	
	public function __construct(RepositoryService $repositories)
	{
		$this->repositories = $repositories;
	}
	
	
	public function __invoke($repositoryName, $fileId)
    {
        return $this->sendFile($repositoryName, $fileId);
    }
    
    public function sendFile($repositoryName, $fileId)
    {
        $repository = $this->repositories->get($repositoryName);
        $file       = $repository->find($fileId);
        $response   = $this->getController()->getResponse();
        
        if (!$file) {
            $response->setStatusCode(404);
            return;
        }
        
        $response->getHeaders()->addHeaderline('Content-Type', $file->type)
                               ->addHeaderline('Content-Length', $file->size);
        $response->sendHeaders();
        
        $resource = $file->getResource();
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
    static public function factory(ContainerInterface $container)
    {
    	$repositories = $container->get('repositories');
    	return new static($repositories);
    }
}
