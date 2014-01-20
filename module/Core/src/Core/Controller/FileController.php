<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileController.php */ 
namespace Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Exception\UnauthorizedImageAccessException;
use Auth\Exception\UnauthorizedAccessException;

class FileController extends AbstractActionController
{
    
    public function indexAction()
    {
        $fileStoreName = $this->params('filestore');
        list($module, $entityName) = explode('.', $fileStoreName);
        $response      = $this->getResponse();
        
        try {
            $repository = $this->getServiceLocator()->get('repositories')->get($module . '/' . $entityName);
        } catch (\Exception $e) {
            $response->setStatusCode(404);
            $this->getEvent()->setParam('exception', $e);
            return;
        }
        $fileId = $this->params('fileId', 0);
        if (preg_match('/^(.*)\..*$/', $fileId, $baseFileName)) {
            $fileId = $baseFileName[1];
        }
        $file       = $repository->find($fileId);
                
        if (!$file) {
            $response->setStatusCode(404);
            return;
        }
        $this->acl($file);
        
        $response->getHeaders()->addHeaderline('Content-Type', $file->type)
                               ->addHeaderline('Content-Length', $file->length);
        $response->sendHeaders();
        
        $resource = $file->getResource();
        
        while (!feof($resource)) {
            echo fread($resource, 1024);
        }
        return $response;
    }
}

