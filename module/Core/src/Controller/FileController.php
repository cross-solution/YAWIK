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

use Core\Entity\FileInterface;
use Core\Entity\ImageInterface;
use Core\EventManager\EventManager;
use Core\Listener\Events\FileEvent;
use Core\Service\FileManager;
use Core\Repository\RepositoryService;
use Cv\Entity\Attachment;
use Interop\Container\ContainerInterface;
use Organizations\Entity\OrganizationImage;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Core\Entity\PermissionsInterface;

/**
 * Class FileController
 *
 * @method \Acl\Controller\Plugin\Acl acl()
 * @package Core\Controller
 */
class FileController extends AbstractActionController
{
    /**
     * @var \Core\Service\FileManager
     */
    private FileManager $fileManager;
    
    /**
     * @var EventManager
     */
    private EventManager $coreFileEvents;
    
    public function __construct(
        FileManager $fileManager,
        EventManager $eventManager
    ) {
        $this->fileManager = $fileManager;
        $this->coreFileEvents = $eventManager;
    }
    
    
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

    /**
     * @return null|FileInterface|ImageInterface
     */
    protected function getFile()
    {
        $fileStoreName = $this->params('filestore');
        list($module, $entityName) = explode('.', $fileStoreName);
        $response      = $this->getResponse();
        $fileManager = $this->fileManager;
        $entityClass = $module . '\\Entity\\' . $entityName;
        $fileId = $this->params('fileId', 0);

        if (preg_match('/^(.*)\..*$/', $fileId, $baseFileName)) {
            $fileId = $baseFileName[1];
        }

        $file = $fileManager->findByID($entityClass, $fileId);
                
        if (!$file) {
            $response->setStatusCode(404);
        }
        return $file;
    }

    /**
     * @return \Laminas\Http\PhpEnvironment\Response
     */
    public function indexAction()
    {
        /* @var \Laminas\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        /* @var \Core\Entity\FileInterface $file */
        $file     = $this->getFile();
        
        if (!$file) {
            return $response;
        }

        $metadata = $file->getMetadata();
        $headers = $response->getHeaders();
        $fileManager = $this->fileManager;

        $this->acl($metadata);

        $headers->addHeaderline('Content-Type', $metadata->getContentType())
            ->addHeaderline('Content-Length', $file->getLength());

        if ($file instanceof OrganizationImage) {
            $expireDate = new \DateTime();
            $expireDate->add(new \DateInterval('P1Y'));
        }

        $response->sendHeaders();
        
        $resource = $fileManager->getStream($file);
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
            $message = ($ex = $this->getEvent()->getParam('exception')) ? $ex->getMessage() : 'File not found.';
            return new JsonModel(
                array(
                    'result' => false,
                    'message' => $message
                )
            );
        }

        $metadata = $file->getMetadata();
        $this->acl($metadata, PermissionsInterface::PERMISSION_CHANGE);


        /* @var \Core\EventManager\EventManager $events */
        $events = $this->coreFileEvents;
        $event = $events->getEvent(FileEvent::EVENT_DELETE, $this, ['file' => $file]);

        $results = $events->triggerEventUntil(function ($r) {
            return true === $r;
        }, $event);

        if(true !== $results->last()){
            $this->fileManager->remove($file, true);
        }

        return new JsonModel(array(
            'result' => true
        ));
    }
}
