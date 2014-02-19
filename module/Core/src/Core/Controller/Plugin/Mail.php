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
     protected $config;
     
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
        $this->config = array();
        return $this;
    }
    
    public function template($template) {
        $controller =  get_class($this->controller);
        $services = $this->getController()->getServiceLocator();
        // get all loaded modules
        $moduleManager = $services->get('ModuleManager');
        $loadedModules = $moduleManager->getModules();
        //get_called_class
        $controllerIdentifier = strtolower(substr($controller, 0, strpos($controller, '\\')));
        $viewResolver = $this->getController()->getServiceLocator()->get('ViewResolver');
                
        $templateHalf = 'mail/' . $template;
        $resource = $viewResolver->resolve($templateHalf);
        
        if (empty($resource)) {
            $templateFull = $controllerIdentifier . '/mail/' . $template;
            $resource = $viewResolver->resolve($templateFull);
        }
        
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
            $__vars = get_defined_vars ();
            foreach ($this->param as $key => $value) {
                if (isset($__vars[$key])) {
                    unset($__vars[$key]);
                }
            }
            unset($__vars['content'],$__vars['controllerIdentifier'],$__vars['controller'],$__vars['resource'],$__vars['template'],$__vars['viewResolver']);
            $this->config = $__vars;
        }
    }
    
    public function informationComplete() {
        $log = $this->getController()->getServiceLocator()->get('Log/Core/Cam');
        if (isset($this->config['templateFull'])) {
            $template = $this->config['templateFull'];
        }
        elseif (isset($this->config['templateHalf'])) {
            $template = $this->config['templateHalf'];
        }
        else {
              throw new \InvalidArgumentException('Not template provided for Mail.');
        }
        if (isset($this->config['from'])) {
            $from = $this->config['from'];
        }
        else {
              throw new \InvalidArgumentException('A from email address must be provided (Variable $from) in Template: ' . $template);
        }
        if (isset($this->config['fromName'])) {
            $fromName = $this->config['fromName'];
        }
        else {
              throw new \InvalidArgumentException('A from name must be provided (Variable $fromName) in Template: ' . $template);
        }
        if (isset($this->config['subject'])) {
            $subject = $this->config['subject'];
        }
        else {
              throw new \InvalidArgumentException('A subject must be provided (Variable $subject) in Template: ' . $template);
        }
        
        $this->setFrom($from, $fromName);
        $this->setSubject($subject);
        return $this;
    }
    
    
    public function send()
    {
        $this->getHeaders()->addHeaderLine('X-Mailer', 'php/Cross Applicant Management');
        //foreach (array('ASCII', 'UTF-8', 'ISO-8859-1', 'ISO-8859-15', 'ISO-8859-7') as $encoding) {
        $encoding = 'UTF-8';
        //$this->getHeaders()->addHeaderLine('charset', $encoding);
        $this->getHeaders()->addHeaderLine('Content-Type', 'text/plain; charset=UTF-8');
        //$this->getHeaders()->setEncoding($encoding);
        $transport = new Sendmail();
        $erg = False;
        try {
            $transport->send($this);
            $erg = True;
        } catch (Exception $e) {
            //$this->getController()->getServiceLocator()->get('Log/Core/Cam')->warn('Mail failure ' . $e->getMessage());
        }
        //}
        return $erg;
    }
}