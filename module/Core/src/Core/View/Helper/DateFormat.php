<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\I18n\View\Helper\DateFormat as ZfDateFormat;
use IntlDateFormatter;


/**
 * @todo Write factory, configuration must be possible
 * @author mathias
 *
 */


class DateFormat extends ZfDateFormat
{
    const FULL  = 'full';
    const LONG  = 'long';
    const MEDIUM= 'medium';
    const SHORT = 'short';
    const NONE  = 'none';
    
    public function __invoke(
        $date,
        $dateType = self::SHORT,
        $timeType = null,
        $locale   = null,
        $pattern  = null
    ) {
        if (is_string($dateType)) {
            $dateType = $this->detectType($dateType);
            if (null === $timeType) {
                $timeType = $dateType;
            }
        }
        if (is_string($timeType)) {
            $timeType = $this->detectType($timeType);
        }
        return parent::__invoke($date, $dateType, $timeType, $locale, $pattern);
    }
    
    protected function detectType($type)
    {
        $type     = strtoupper($type);
        $constant = "\IntlDateFormatter::$type";
        $value    = @constant($constant);
        return null !== $value ? $value : IntlDateFormatter::NONE;
        
    }
}