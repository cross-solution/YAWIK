<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Notification.php */ 
namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class Notification extends AbstractPlugin
{
    const NAMESPACE_INFO = 'info';
    const NAMESPACE_WARNING = 'warning';
    const NAMESPACE_DANGER  = 'danger';
    const NAMESPACE_SUCCESS  = 'success';
    
    protected $namespace = self::NAMESPACE_INFO;
    
    protected $flashMessenger;
    
    public function __construct(FlashMessenger $flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
    }
    
    public function __invoke($message = null, $namespace = self::NAMESPACE_INFO)
    {
        return null === $message
               ? $this
               : $this->addMessage($message, $namespace);
    }
    
    public function addMessage($message, $namespace = self::NAMESPACE_INFO)
    {
        $origNamespace = $this->flashMessenger->getNamespace();
        $this->flashMessenger
             ->setNamespace($namespace)
             ->addMessage($message)
             ->setNamespace($origNamespace);
        
        return $this;
    }
    
    public function info($message)
    {
        return $this->addMessage($message, self::NAMESPACE_INFO);
    }
    
    public function warning($message) 
    {
        return $this->addMessage($message, self::NAMESPACE_WARNING);
    }
    
    public function success($message) 
    {
        return $this->addMessage($message, self::NAMESPACE_SUCCESS);
    }

    public function danger($message)
    {
        return $this->addMessage($message, self::NAMESPACE_DANGER);
    }
    
    public function error($message)
    {
        return $this->addMessage($message, self::NAMESPACE_DANGER);
    }
}

