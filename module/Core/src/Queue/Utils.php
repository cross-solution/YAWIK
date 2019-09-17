<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
final class Utils
{
    public static function createDateInterval($value)
    {
        if ($value instanceOf \DateInterval) {
            return $value;
        }

        if (is_numeric($value)) {
            $delay = new \DateInterval(sprintf("PT%dS", abs((int) $value)));
            $delay->invert = ($value < 0) ? 1 : 0;

            return $delay;
        }

        if (is_string($value)) {
            try {
                // first try ISO 8601 duration specification
                $delay = new \DateInterval($value);
            } catch (\Exception $e) {
                // then try normal date parser
                $delay = \DateInterval::createFromDateString($value);
            }
            return $delay;
        }

        return new \DateInterval('PT0S');
    }

    public static function createDateTime($value)
    {
        if ($value instanceOf \DateTime) {
            return $value;
        }

        if (is_numeric($value)) {
            return new \DateTime(
                sprintf("@%d", (int) $value),
                new \DateTimeZone(date_default_timezone_get())
            );
        }

        if (is_string($value)) {
            return new \DateTime($value, new \DateTimeZone(date_default_timezone_get()));
        }

        return new \DateTime();
    }
}
