<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helpers */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\I18n\View\Helper\DateFormat as ZfDateFormat;
use IntlDateFormatter;

/**
 * Helper to format DateTime objects to localized strings.
 *
 * This is an enhancement of \Zend\I18n\View\Helper\DateFormat.
 *
 * <code>
 *      // see ZF2 docs for date format types.
 *
 *      // Formats date and time in LONG format
 *      $this->formatDate($date, DateFormat::LONG);
 *
 *      // Formats date and time in SHORT format
 *      $this->formatDate($date);
 *      $this->formatDate($date, 'short');
 *
 *      // Formats date in long and time in short format
 *      $this->formatDate($date, 'long', 'short');
 *
 * </code>
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DateFormat extends ZfDateFormat
{
    const FULL  = 'full';
    const LONG  = 'long';
    const MEDIUM= 'medium';
    const SHORT = 'short';
    const NONE  = 'none';

    /**
     * Formats a date.
     *
     * Pass <b>$dateTime</b> and/or <b>$timeType</b> as string or constant.
     * Sets <b>$timeType</b> to same format as <b>$dateType</b> if not given.
     * Proxies to parent method for rendering.
     *
     * @see \Zend\I18n\View\Helper\DateFormat::__invoke()
     *
     * @param \DateTime $data|string
     * @param string $dateType
     * @return string
     */
    public function __invoke(
        $date,
        $dateType = self::SHORT,
        $timeType = null,
        $locale = null,
        $pattern = null
    ) {
        if (is_string($dateType)) {
            $dateType = $this->detectType($dateType);
            if (null === $timeType) {
                $timeType = $dateType;
            }
        }
        if (is_string($date)) {
            $date = date_create($date);
        }
        if (is_string($timeType)) {
            $timeType = $this->detectType($timeType);
        }
        return parent::__invoke($date, $dateType, $timeType, $locale, $pattern);
    }
    
    /**
     * Maps strings and constants to IntlDateFormatter constants (int).
     *
     * @param string $type
     * @return int
     */
    protected function detectType($type)
    {
        $type     = strtoupper($type);
        $constant = "\IntlDateFormatter::$type";
        $value    = @constant($constant);
        return null !== $value ? $value : IntlDateFormatter::NONE;
    }
}
