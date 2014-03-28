<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\EventManager\Event;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ArrayUtils;

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
        
        $event = new Event();
        $eventManager = $services->get('EventManager');
        $eventManager->setIdentifiers('Mail');
        $p = new Parameters(array('mail' => $this, 'template' => $template));
        $event->setParams($p);         
        $eventManager->trigger('template.pre', $event);
        
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
        $log = $this->getController()->getServiceLocator()->get('Log/Core/Mail');
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
            $log->err('A from email address must be provided (Variable $from) in Template: ' . $template);
              throw new \InvalidArgumentException('A from email address must be provided (Variable $from) in Template: ' . $template);
        }
        if (isset($this->config['fromName'])) {
            $fromName = $this->config['fromName'];
        }
        else {
            $log->err('A from name must be provided (Variable $fromName) in Template: ' . $template);
              throw new \InvalidArgumentException('A from name must be provided (Variable $fromName) in Template: ' . $template);
        }
        if (isset($this->config['subject'])) {
            $subject = $this->config['subject'];
        }
        else {
            $log->err('A subject must be provided (Variable $subject) in Template: ' . $template);
              throw new \InvalidArgumentException('A subject must be provided (Variable $subject) in Template: ' . $template);
        }
        $this->setFrom($from, $fromName);
        $this->setSubject($subject);
        
        $toA = ArrayUtils::iteratorToArray($this->getTo());
        $to = '';
        if (!empty($toA)) {
            $to = array_shift($toA)->toString();
        }
        $ccA = ArrayUtils::iteratorToArray($this->getCc());
        $cc = '';
        if (!empty($CcA)) {
            $Cc = array_shift($CcA)->toString();
        }
        $bccA = ArrayUtils::iteratorToArray($this->getBcc());
        $bcc = '';
        if (!empty($bccA)) {
            $bcc = array_shift($bccA)->toString();
        }
        $fromA = ArrayUtils::iteratorToArray($this->getFrom());
        $from = '';
        if (!empty($fromA)) {
            $from = array_shift($fromA)->toString();
        }
        $replyToA = ArrayUtils::iteratorToArray($this->getReplyTo());
        $replyTo = '';
        if (!empty($replyToA)) {
            $replyTo = array_shift($replyToA)->toString();
        }
        //ArrayUtils::iteratorToArray($this->getSender());
        $log->info(str_pad($template,30) 
                . 'to: ' . str_pad($to,50) 
                . 'cc: ' . str_pad($bc,50) 
                . 'bcc: ' . str_pad($bcc,50) 
                . 'from: ' . str_pad($from,50) 
                . 'replyTo: ' . str_pad($replyTo,50) 
                //. str_pad(implode(',', ArrayUtils::iteratorToArray($this->getSender())),50) 
                . 'subject: ' . str_pad($this->getSubject(),50));
        return $this;
    }
    
    
    public function send()
    {
        $this->getHeaders()->addHeaderLine('X-Mailer', 'php/YAWIK');
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