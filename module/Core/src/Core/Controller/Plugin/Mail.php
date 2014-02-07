<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

class mail extends Message implements PluginInterface
{
     protected $controller;
     protected $param;
     
    /**
     * Set the current controller instance
     *
     * @param  Dispatchable $controller
     * @return void
     */
    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get the current controller instance
     *
     * @return null|Dispatchable
     */
    public function getController()
    {
        return $this->controller;
    }
    
    public function __invoke(array $param = array())
    {   
        $this->param = $param;
        return $this;
    }
    
    public function template($template) {
        $controller =  get_class($this->controller);
        //get_called_class
        $controllerIdentifier = strtolower(substr($controller, 0, strpos($controller, '\\')));
                
        $viewResolver = $this->getController()->getServiceLocator()->get('ViewResolver');
        $resource = $viewResolver->resolve($controllerIdentifier . '/mail/' . $template);
        
        $__vars = $this->param;
        if (array_key_exists('this', $__vars)) {
            unset($__vars['this']);
        }
        extract($__vars);
        unset($__vars); // remove $__vars from local scope
        
        if ($resource) {
            try {
                ob_start();
                include $resource;
                $content = ob_get_clean();
                $this->setBody($content);
            } catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
        }
    }
    
    
    public function send()
    {
        $this->getHeaders()->addHeaderLine('X-Mailer', 'php/Cross Applicant Management');
        $transport = new Sendmail();
        $erg = False;
        try {
            $transport->send($this);
            $erg = True;
        } catch (Exception $e) {
             //$this->getController()->getServiceLocator()->get('Log')->warn('Mail failure ' . $e->getMessage());
        }
        return $erg;
    }
}