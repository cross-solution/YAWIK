<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Message.php */ 
namespace Core\Mail;

use Zend\Mail\Message as ZfMessage;
use Zend\Mail\Transport\TransportInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Message extends ZfMessage
{
    
    public function __construct(array $options=array()) {
        $this->setOptions($options);
    }
    
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceOf \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Expected $options to be an array or \Traversable, but received %s',
                (is_object($options) ? 'instance of ' . get_class($options) : 'skalar')
            ));
        }
        
        foreach ($options as $key => $value) {
            $method = "set$key";
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
}

