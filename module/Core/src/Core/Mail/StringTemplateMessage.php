<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** TextTemplateMessage.php */ 
namespace Core\Mail;

use Zend\Mail\Message;
use Zend\Mail\Header;

class StringTemplateMessage extends TranslatorAwareMessage
{
    protected $variables;
    protected $callbacks;
    protected $template;
    
    
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->getHeaders()->addHeader(Header\ContentType::fromString('Content-Type: text/plain; charset=UTF-8'));
        $this->setEncoding('UTF-8');
    }
    
    public function setVariables($variables=array())
    {
        $this->variables = array();
        return $this->addVariables($variables);
    }
    
    public function addVariables($variables=array())
    {
        if (!is_array($variables)) {
            if (!$variables instanceOf \Traversable) {
                throw new \InvalidArgumentException(sprintf(
                    'Expect an array or an instance of \Traversable, but received %s',
                    is_object($variables) ? 'instance of ' . get_class($variables) : 'skalar'
                ));
            }
            $variables = \Zend\Stdlib\ArrayUtils::iteratorToArray($variables);
        }
        
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }
    
    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }
    
    public function setCallbacks($callbacks = array())
    {
        $this->callbacks = array();
        return $this->addCallbacks($callbacks);
    }
    
    public function addCallbacks($callbacks = array())
    {
        if (!is_array($callbacks)) {
            if (!$callbacks instanceOf \Traversable) {
                throw new \InvalidArgumentException(sprintf(
                    'Expect an array or an instance of \Traversable, but received %s',
                    is_object($callbacks) ? 'instance of ' . get_class($callbacks) : 'skalar'
                ));
            }
        }
        
        foreach ($this->callbacks as $name => $callback) {
            $this->setCallback($name, $callback);
        }
        return $this;
    }
    
    public function setCallback($name, $callable)
    {
        if (!is_string($callable) && !is_callable($callable)) {
            throw new \InvalidArgumentException('Provided callback is not callable');
        }
        $this->callbacks[$name] = $callable;
    }
    
    public function getBodyText()
    {
        $body = parent::getBodyText();
        $body = $this->parseVariables($body);
        $body = $this->parseCallbacks($body);
        
        if (preg_match('~\+\+subject:(?P<subject>.*?)\+\+~is', $body, $match)) {
            $this->setSubject($match['subject']);
            $body = str_replace($match[0], '', $body);
        }
        
        return $body;
    }
    
    protected function parseVariables($body)
    {
        if (null === $this->variables) {
            return $body;
        }
        
        $variableNames = array_map(array($this, 'getNamePattern'), array_keys($this->variables));
        $variableValues = array_values($this->variables);
        $body = preg_replace($variableNames, $variableValues, $body);
        return $body;
    }
    
    protected function parseCallbacks($body)
    {
        if (null == $this->callbacks) {
            return $body;
        }
        
        foreach ($this->callbacks as $name => $callable) {
            $pattern = $this->getNamePattern($name);
            
            if (preg_match($pattern, $body)) {
                if (is_string($callable)) {
                    if (!method_exists($this, $callable)) {
                        continue;
                    }
                    $value = $this->$callable();
                } else {
                    $value = call_user_func($callable);
                }
                $body = preg_replace($pattern, $value, $body);
            }
        }
        return $body;
    }
    
    protected function getNamePattern($name)
    {
        return '~##' . preg_quote($name) . '##~is';
    }
}

