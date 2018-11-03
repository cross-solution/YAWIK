<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ErrorType.php */
namespace Core\Log\Filter;

use Zend\Log\Filter\FilterInterface;

class ErrorType implements FilterInterface
{
    const TYPE_ERROR = 1;
    const TYPE_EXCEPTION = 2;
    
    protected $type;
    
    public function __construct($type)
    {
        if (self::TYPE_ERROR != $type && self::TYPE_EXCEPTION != $type) {
            throw new \InvalidArgumentException('Type must be given as either TYPE_ERROR or TYPE_EXCEPTION');
        }
        
        $this->type = $type;
    }
    
    public function filter(array $event)
    {
        $isError = isset($event['extra']['errno']);
        
        return self::TYPE_ERROR == $this->type ? $isError : !$isError;
    }
}
