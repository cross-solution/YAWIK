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

class FileController extends AbstractActionController
{
    
    public function indexAction()
    {
        $fileStoreName = $this->params('filestore');
        $response      = $this->getResponse();
        
        try {
            $repository = $this->getServiceLocator()->get('repositories')->get($fileStoreName . '/Files');
        } catch (\Exception $e) {
            $response->setStatusCode(404);
            $this->getEvent()->setParam('exception', $e);
            return;
        }
        
        $file       = $repository->find($this->params('fileId', 0));
                
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
        return $response;
    }
}

