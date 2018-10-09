<?php

namespace Core\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\AddressList;
use Zend\EventManager\Event;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Resolver\ResolverInterface;

class Mail extends Message implements PluginInterface
{
    /**
     * @var Dispatchable
     */
    protected $controller;
    
    /**
     * @var array
     */
    protected $param = array();
    
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var LoggerInterface
     */
    protected $mailLogger;

    /**
     * @var ResolverInterface
     */
    protected $viewResolver;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var ModuleManagerInterface
     */
    protected $moduleManager;

    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * Mail constructor.
     * @param LoggerInterface $mailLogger
     * @param ResolverInterface $viewResolver
     * @param EventManagerInterface $eventManager
     * @param ModuleManagerInterface $moduleManager
     */
    public function __construct(
        LoggerInterface $mailLogger,
        ResolverInterface $viewResolver,
        EventManagerInterface $eventManager,
        ModuleManagerInterface $moduleManager
    )
    {
        $this->mailLogger       = $mailLogger;
        $this->viewResolver     = $viewResolver;
        $this->eventManager     = $eventManager;
        $this->moduleManager    = $moduleManager;
        $this->transport        = new Sendmail();
    }
    
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

    /**
     * Set mail transport to be use
     *
     * @param TransportInterface $transport
     * @return $this
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    public function __invoke(array $param = array())
    {
        $this->param = $param;
        $this->config = array();
        return $this;
    }
    
    protected function stringFromMailHeader($header)
    {
        $erg = '';
        if ($header instanceof AddressList) {
            $A = ArrayUtils::iteratorToArray($header);
            $AA = array();
            while (!empty($A)) {
                $AA[] = array_shift($A)->toString();
            }
            $erg = implode(',', $AA);
        }
        return $erg;
    }
    
    public function __toString()
    {
        $template = $this->getTemplate();
        $to = $this->stringFromMailHeader($this->getTo());
        $cc = $this->stringFromMailHeader($this->getCc());
        $bcc = $this->stringFromMailHeader($this->getBcc());
        $from = $this->stringFromMailHeader($this->getFrom());
        $replyTo = $this->stringFromMailHeader($this->getReplyTo());
        
        return str_pad($template, 30)
                . 'to: ' . str_pad($to, 50)
                . 'cc: ' . str_pad($cc, 50)
                . 'bcc: ' . str_pad($bcc, 50)
                . 'from: ' . str_pad($from, 50)
                . 'replyTo: ' . str_pad($replyTo, 50)
                //. str_pad(implode(',', ArrayUtils::iteratorToArray($this->getSender())),50)
                . 'subject: ' . str_pad($this->getSubject(), 50);
    }
    
    public function template($template)
    {
        $controller =  is_object($this->controller) ? get_class($this->controller):'null';
        
        $event = new Event();
        $eventManager = $this->eventManager;
        // @TODO: check if change this value into ['Mail'] not causing any errors!
        $eventManager->setIdentifiers(['Mail']);
        $p = new Parameters(array('mail' => $this, 'template' => $template));
        $event->setParams($p);
        $eventManager->trigger('template.pre', $event);
        
        // get all loaded modules
        $moduleManager = $this->moduleManager;
        $loadedModules = $moduleManager->getModules();
        //get_called_class
        $controllerIdentifier = strtolower(substr($controller, 0, strpos($controller, '\\')));
        $viewResolver = $this->viewResolver;
                
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
            $__vars = get_defined_vars();
            foreach ($this->param as $key => $value) {
                if (isset($__vars[$key])) {
                    unset($__vars[$key]);
                }
            }
            unset($__vars['content'],$__vars['controllerIdentifier'],$__vars['controller'],$__vars['resource'],$__vars['template'],$__vars['viewResolver']);
            $this->config = $__vars;
        }
    }
    
    protected function getTemplate()
    {
        $template = null;
        if (isset($this->config['templateFull'])) {
            $template = $this->config['templateFull'];
        } elseif (isset($this->config['templateHalf'])) {
            $template = $this->config['templateHalf'];
        } else {
              throw new \InvalidArgumentException('No template provided for Mail.');
        }
        return $template;
    }
    
    public function informationComplete()
    {
        $log = $this->mailLogger;
        $template = $this->getTemplate();
        if (isset($this->config['from'])) {
            $from = $this->config['from'];
        } else {
            $log->err('A from email address must be provided (Variable $from) in Template: ' . $template);
            throw new \InvalidArgumentException('A from email address must be provided (Variable $from) in Template: ' . $template);
        }
        if (isset($this->config['fromName'])) {
            $fromName = $this->config['fromName'];
        } else {
            $log->err('A from name must be provided (Variable $fromName) in Template: ' . $template);
            throw new \InvalidArgumentException('A from name must be provided (Variable $fromName) in Template: ' . $template);
        }
        if (isset($this->config['subject'])) {
            $subject = $this->config['subject'];
        } else {
            $log->err('A subject must be provided (Variable $subject) in Template: ' . $template);
            throw new \InvalidArgumentException('A subject must be provided (Variable $subject) in Template: ' . $template);
        }
        $this->setFrom($from, $fromName);
        $this->setSubject($subject);
        return $this;
    }

    public function send()
    {
        $log = $this->mailLogger;
        $this->getHeaders()->addHeaderLine('X-Mailer', 'php/YAWIK');

        $this->getHeaders()->addHeaderLine('Content-Type', 'text/plain; charset=UTF-8');

        $transport = $this->transport;
        $erg = false;
        try {
            $transport->send($this);
            $erg = true;
            $log->info($this);
        } catch (\Exception $e) {
            $log->err('Mail failure ' . $e->getMessage());
        }
        return $erg;
    }
    
	/**
	 * @param ContainerInterface $container
	 *
	 * @return static
	 */
    public static function factory(ContainerInterface $container)
    {
        //@TODO: need to define transport to be use during ::send()
        $mailLog        = $container->get('Log/Core/Mail');
        $viewResolver   = $container->get('ViewResolver');
        $eventManager   = $container->get('EventManager');
        $moduleManager  = $container->get('ModuleManager');
        return new static($mailLog,$viewResolver,$eventManager,$moduleManager);
    }
}
