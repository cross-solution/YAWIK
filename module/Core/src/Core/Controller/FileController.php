<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileController.php */
namespace Core\Controller;

use Organizations\Entity\OrganizationImage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use Core\Entity\PermissionsInterface;

/**
 * Class FileController
 *
 * @method \Acl\Controller\Plugin\Acl acl()
 * @package Core\Controller
 */
class FileController extends AbstractActionController
{
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);

        $serviceLocator  = $this->serviceLocator;
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events->attach($defaultServices);
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
            $repository = $this->serviceLocator->get('repositories')->get($module . '/' . $entityName);
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

    /**
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public function indexAction()
    {
        /* @var \Zend\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        /* @var \Core\Entity\FileEntity $file */
        $file     = $this->getFile();
        
        if (!$file) {
            return $response;
        }
        
        $this->acl($file);

        $headers=$response->getHeaders();

        $headers->addHeaderline('Content-Type', $file->getType())
            ->addHeaderline('Content-Length', $file->getLength());

        if ($file instanceof OrganizationImage) {
            $expireDate = new \DateTime();
            $expireDate->add(new \DateInterval('P1Y'));

//            $headers->addHeaderline('Expires', $expireDate->format(\DateTime::W3C))
//                ->addHeaderLine('ETag', $file->getId())
//                ->addHeaderline('Cache-Control', 'public')
//                ->addHeaderline('Pragma', 'cache');
        }

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
            return new JsonModel(
                array(
                'result' => false,
                'message' => ($ex = $this->getEvent()->getParam('exception'))
                             ? $ex->getMessage()
                             : 'File not found.'
                )
            );
        }
        
        $this->acl($file, PermissionsInterface::PERMISSION_CHANGE);
        $this->serviceLocator->get('repositories')->remove($file);
        return new JsonModel(
            array(
            'result' => true
            )
        );
    }
}
