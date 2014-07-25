<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileController.php */ 
namespace Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Exception\UnauthorizedImageAccessException;
use Auth\Exception\UnauthorizedAccessException;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use Core\Entity\PermissionsInterface;

class FileController extends AbstractActionController
{
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
    }
    
    public function preDispatch(MvcEvent $e)
    {
        if ('delete' == $this->params()->fromQuery('do') && $this->getRequest()->isXmlHttpRequest()) {
            $routeMatch = $e->getRouteMatch();
            $routeMatch->setParam('action', 'delete');
        }
    }
    
    protected function getFile()
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
        }
        return $file;
    }
    
    public function indexAction()
    {
        $response = $this->getResponse();
        $file     = $this->getFile();
        
        if (!$file) {
            return $response;
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
    
    public function deleteAction()
    {
        $file = $this->getFile();
        if (!$file) {
            $this->response->setStatusCode(500);
            return new JsonModel(array(
                'result' => false,
                'message' => $ex = $this->getEvent()->getParam('exception') 
                             ? $ex->getMessage()
                             : 'File not found.'
            ));
        }
        
        $this->acl($file, PermissionsInterface::PERMISSION_CHANGE);
        $this->getServiceLocator()->get('repositories')->remove($file);
        return new JsonModel(array(
            'result' => true
        ));
    }
}

