<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper;

use Zend\View\ViewEvent;
use Core\View\Helper\InsertFile\FileEvent;

class InsertFile extends AbstractEventsHelper {
    
    protected $files = array();
    
    protected $ListenersUnaware = True;
    
    protected $event;
        
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
    
    protected function getEvent() {
        if (!isset($this->event)) {
            $this->event = new FileEvent();
        }
        return $this->event;
    }
       
    /**
     * hook into the rendering-process to provide a summary of all included files
     */
    public function listenToRenderer() {
        if ($this->ListenersUnaware) {
            // set a listener at the end of the Rendering-Process
            // to announce what files have been inserted
            $this->ListenersUnaware = False;
            $services = $this->getServiceLocator();
        
            $viewManager = $services->get('ViewManager');
            $view = $viewManager->getView();
            $viewEvents = $view->getEventManager();
            // rendering ist over
            // get the attached Files very early
            // before any other postprocessing has started
            $viewEvents->attach(ViewEvent::EVENT_RESPONSE, array($this, 'anounceAttachedFiles'), 1000);
        }
    }
    
    public function anounceAttachedFiles(ViewEvent $e) {
        $event = $this->getEvent();
        $this->trigger(FileEvent::INSERTFILE, $event);
    }
   
}

