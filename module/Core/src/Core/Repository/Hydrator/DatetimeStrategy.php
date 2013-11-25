<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class DatetimeStrategy implements StrategyInterface
{
    
    const FORMAT_MONGO           = 'MONGO';
    const FORMAT_ISO             = 'ISO';
    const FORMAT_MYSQLDATE       = 'MYSQLDATE';
    const FORMAT_MYSQLDATETIME   = 'MYSQLDATETIME';
    
    protected $extractFormat;
    protected $hydrateFormat;
    
    public function __construct($hydrateFormat=self::FORMAT_MONGO, $extractFormat=self::FORMAT_MONGO)
    {
        $this->setHydrateFormat($hydrateFormat);
        $this->setExtractFormat($extractFormat);
    }
    
    public function setHydrateFormat($format)
    {
        $this->hydrateFormat = $format;
        return $this;
    }
    
    public function getHydrateFormat()
    {
        return $this->hydrateFormat;
    }
    
    public function setExtractFormat($format)
    {
        $this->extractFormat = $format;
        return $this;
    }
    
    public function getExtractFormat()
    {
        return $this->extractFormat;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if ($value instanceOf \DateTime) {
            return $value;
        }
        switch ($this->hydrateFormat) {
            case self::FORMAT_MONGO:
                return $this->hydrateFromMongoFormat($value);
                break;
                
            case self::FORMAT_ISO:
                return $this->hydrateFromIsoFormat($value);
                break;
            
            case self::FORMAT_MYSQLDATE:
                return $this->hydrateFromMysqlDateFormat($value);
                break;
            
            case self::FORMAT_MYSQLDATETIME:
                return $this->hydrateFromMysqlDatetimeFormat($value);
                break;
                
            default:
                die (__METHOD__ . ': Unknown format.');
                break;
        }
    }
    
    protected function hydrateFromMongoFormat($value)
    {
        if (!is_array($value) || !isset($value['date']) || !isset($value['tz'])) {
            // @todo Error Handling.
            return "";
        }
        $date = new \DateTime("@".$value['date']->sec);
        $date->setTimezone(new \DateTimeZone($value['tz']));
        return $date;
    }
    
    protected function hydrateFromIsoFormat($value)
    {
        $date = \DateTime::createFromFormat('c', $value);
        return $date;
    }
    
    protected function hydrateFromMysqlDateFormat($value)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value . ' 01:00:00');
        return $date;
    }
    
    protected function hydrateFromMysqlDatetimeFormat($value)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
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
         
        switch ($this->extractFormat) {
            case self::FORMAT_MONGO:
                return $this->extractToMongoFormat($value);
                break;
        
            case self::FORMAT_ISO:
                return $this->extractToIsoFormat($value);
                break;
        
            default:
                die (__METHOD__ . ': Unknown format.');
                break;
        }
    }
    
    protected function extractToMongoFormat($value)
    {
        $date = new \MongoDate($value->getTimestamp());
        
        return array(
            'date' => $date,
            'tz' => $value->getTimezone()->getName(),
        );
        
    }
    
    protected function extractToIsoFormat($value)
    {
        return $value->format('c');
    }
    
}