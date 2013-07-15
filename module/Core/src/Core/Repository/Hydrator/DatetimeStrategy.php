<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class DatetimeStrategy implements StrategyInterface
{
    
    protected $extractResetDate;
    
    public function __construct($extractResetDate=false)
    {
        $this->setExtractResetDate($extractResetDate);
    }
    
    public function setExtractResetDate($flag)
    {
        $this->extractResetDate = (bool) $flag;
        return $this;
    }
    
    public function extractResetDate()
    {
        return $this->extractResetDate;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (!is_array($value) || !isset($value['date']) || !isset($value['tz'])) {
            // @todo Error Handling.
            return "";
        }
        $date = new \DateTime("@".$value['date']->sec);
        $date->setTimezone(new \DateTimeZone($value['tz']));
        return $date;
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        if (!$value instanceOf \DateTime) {
            // @todo Error Handling
            return "";
        }
        $date = $this->extractResetDate
              ? new \MongoDate()
              : new \MongoDate($value->getTimestamp());
        
        return array(
            'date' => $date,
            'tz' => $value->getTimezone()->getName(),
        );
        
    }
    
}