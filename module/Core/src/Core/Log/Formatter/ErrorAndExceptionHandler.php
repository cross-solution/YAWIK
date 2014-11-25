<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ErrorAndExceptionHandler.php */ 
namespace Core\Log\Formatter;

use Zend\Log\Formatter\FormatterInterface;
use Zend\Log\Formatter\Simple;

class ErrorAndExceptionHandler implements FormatterInterface
{
    const DEFAULT_ERROR_FORMAT     = '%timestamp% %priorityName% %message% (errno %extra[errno]%) in %extra[file]% on line %extra[line]%';
    const DEFAULT_EXCEPTION_FORMAT = '%timestamp% EXCEPTION %message% in %extra[file]% on line %extra[line]%'; 
    
    protected $errorFormat;
    protected $exceptionFormat;
    
    /**
     * Format specifier for DateTime objects in event data
     *
     * @see http://php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;
    
    public function __construct(array $options=array())
    {
        $options = array_merge(array(
            'errorFormat' => self::DEFAULT_ERROR_FORMAT,
            'exceptionFormat' => self::DEFAULT_EXCEPTION_FORMAT,
            'dateTimeFormat' => self::DEFAULT_DATETIME_FORMAT,
        ), $options);
        
        foreach ($options as $key => $val) {
            $method = "set$key";
            if (method_exists($this, $method)) {
                $this->$method($val);
            }
        }
    }
    
    /** {@inheritdoc} */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /** {@inheritdoc} */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = (string) $dateTimeFormat;
        return $this;
    }

    /**
     * @return String
     */
    public function getErrorFormat ()
    {
        return $this->errorFormat;
    }

    /**
     * @param $errorFormat
     * @return $this
     */
    public function setErrorFormat ($errorFormat)
    {
        $this->errorFormat = $errorFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExceptionFormat ()
    {
        return $this->exceptionFormat;
    }

    /**
     * @param $exceptionFormat
     * @return $this
     */
    public function setExceptionFormat ($exceptionFormat)
    {
        $this->exceptionFormat = $exceptionFormat;
        return $this;
    }

    /**
     * @param array $event
     * @return mixed|string
     */
    public function format($event)
    {
        if (isset($event['timestamp']) && $event['timestamp'] instanceof \DateTime) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }
        
        return isset($event['extra']['errno'])
               ? $this->formatError($event)
               : $this->formatException($event);
    }

    /**
     * @param array $event
     * @return mixed
     */
    protected function formatError(array $event)
    {
        $output = $this->errorFormat;
        
        foreach ($this->buildReplacementsFromArray($event) as $name => $value) {
            $output = str_replace("%$name%", $value, $output);
        }
        
        return $output;
    }
    
    /**
     * Flatten the multi-dimensional $event array into a single dimensional
     * array
     *
     * @param array $event
     * @param string $key
     * @return array
     */
    protected function buildReplacementsFromArray ($event, $key = null)
    {
        $result = array();
        foreach ($event as $index => $value) {
            $nextIndex = $key === null ? $index : $key . '[' . $index . ']';
            if ($value === null) {
                continue;
            }
            if (! is_array($value)) {
                if ($key === null) {
                    $result[$nextIndex] = $value;
                } else {
                    if (! is_object($value) || method_exists($value, "__toString")) {
                        $result[$nextIndex] = $value;
                    }
                }
            } else {
                $result = array_merge($result, $this->buildReplacementsFromArray($value, $nextIndex));
            }
        }
        return $result;
    }
    
    protected function formatException(array $event)
    {
        $search = array();
        $replace = array();
        foreach ($event as $key => $val) {
            if ('extra' == $key) {
                if (isset($val['file'])) {
                    $search[] = "%extra[file]%";
                    $replace[] = $val['file'];
                }
                if (isset($val['line'])) {
                    $search[] = '%extra[line]%';
                    $replace[] = $val['line'];
                }
            } else {
                $search[] = "%$key%";
                $replace[] = $val;
            }
        }
        
        $output = str_replace($search, $replace, $this->exceptionFormat);
        
        if (!empty($event['extra']['trace'])) {
            $outputTrace = '';
            foreach ($event['extra']['trace'] as $trace) {
                foreach ($trace as $key => $val) {
                    if ('args' == $key) {
                        continue;
                    }
                    if ('type' == $key) {
                        $val = $this->getType($val);
                    }
                    $outputTrace .= sprintf(
                        "    %8s: %s\n",
                        ucfirst($key), $val
                    );
                }
                $outputTrace .= str_repeat('-', 30) . PHP_EOL;
        
            }
            $output .= "\n[Trace]\n" . $outputTrace;
        }
        
        return $output;
    }
    
	/**
     * Get the type of a function
     *
     * @param string $type
     * @return string
     */
    protected function getType($type)
    {
        switch ($type) {
            case "::" :
                return "static";
            case "->" :
                return "method";
            default :
                return $type;
        }
    }
}

