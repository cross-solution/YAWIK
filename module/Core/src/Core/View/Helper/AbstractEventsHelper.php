<?php

namespace Core\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\EventManager\EventManager;

abstract class AbstractEventsHelper extends EventManager implements HelperInterface
{
 
    /**
     * View object
     *
     * @var Renderer
     */
    protected $view = null;
    
    /**
     * Set the View object
     *
     * @param  Renderer $view
     * @return AbstractHelper
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }
}
