<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\View\ViewEvent;
use Core\View\Helper\InsertFile\FileEvent;

class InsertFile extends AbstractEventsHelper
{
    /**
     * @var ContainerInterface
     */
    protected $serviceManager;
    
    protected $files = array();
    
    protected $ListenersUnaware = true;
    
    protected $event;

    /**
     * @param ContainerInterface $serviceManager
     * @param string $identifiers
     */
    public function __construct(ContainerInterface $serviceManager, $identifiers = null)
    {
        parent::__construct($identifiers);
        $this->serviceManager = $serviceManager;
    }
    
    /**
     * render a File-Object
     *
     * @param string
     * @return string
     */
    public function __invoke($fileName, $parameter = array())
    {
        $this->listenToRenderer();
        $event = $this->getEvent();
        $event->addFilename($fileName);
        $event->setRenderParameter($parameter);
        $return = 'file not found';
        
        // ensure, that we have a file-object
        $result = $this->trigger(FileEvent::GETFILE, $event);
        if (!empty($result)) {
            // ask someone else for rendering
            $result = $this->trigger(FileEvent::RENDERFILE, $event);
            $return = $result->last();
        }
        return $return;
    }
    
    protected function getEvent()
    {
        if (!isset($this->event)) {
            $this->event = new FileEvent();
        }
        return $this->event;
    }
       
    /**
     * hook into the rendering-process to provide a summary of all included files
     */
    public function listenToRenderer()
    {
        if ($this->ListenersUnaware) {
            // set a listener at the end of the Rendering-Process
            // to announce what files have been inserted
            $this->ListenersUnaware = false;
            $view = $this->serviceManager->get('View');
            $viewEvents = $view->getEventManager();
            // rendering ist over
            // get the attached Files very early
            // before any other postprocessing has started
            $viewEvents->attach(ViewEvent::EVENT_RESPONSE, array($this, 'anounceAttachedFiles'), 1000);
        }
    }
    
    public function anounceAttachedFiles(ViewEvent $e)
    {
        $event = $this->getEvent();
        $this->trigger(FileEvent::INSERTFILE, $event);
    }
	
	/**
	 * @param ContainerInterface $container
	 *
	 * @return InsertFile
	 */
    public static function factory(ContainerInterface $container)
    {
        return new static($container);
    }
}
