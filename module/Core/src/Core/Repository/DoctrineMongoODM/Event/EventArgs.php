<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** EventArgs.php */
namespace Core\Repository\DoctrineMongoODM\Event;

class EventArgs extends \Doctrine\Common\EventArgs
{
    private $values;
    
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }
    
    public function get($key)
    {
        if (!isset($this->values[$key])) {
            throw new \OutOfBoundsException('Invalid key "' . $key . '"');
        }
        return $this->values[$key];
    }
    
    public function set($key, $value)
    {
        $this->values[$key] = $value;
        return $this;
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }
    
    public function __call($method, $params)
    {
        $type   = substr($method, 0, 3);
        
        if ('get' == $type || 'set' == $type) {
            $filter = function ($match) {
                return '_' . strtolower($match[0]);
            };
            $key = lcfirst(substr($method, 3));
            $key = preg_replace_callback('~([A-Z])~', $filter, $key);
            
            return 'get' == $type ? $this->get($key) : $this->set($key, (isset($params[0]) ? $params[0] : null));
        }
        
        throw new \BadMethodCallException('Unknown method: ' . $method);
    }
}
