<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * Job salary entity.
 *
 * @ODM\EmbeddedDocument
 */
class Salary extends AbstractEntity
{
    /**#@+
     * Time unit interval constants.
     *
     * @var string
     */
    const UNIT_HOUR  = 'HOUR';
    const UNIT_DAY   = 'DAY';
    const UNIT_WEEK  = 'WEEK';
    const UNIT_MONTH = 'MONTH';
    const UNIT_YEAR  = 'YEAR';
    /**#@-*/

    /**
     * The currency code.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $currency;

    /**
     * Salary amount value.
     *
     * @var float
     * @ODM\Field(type="float")
     */
    protected $value;

    /**
     * Salary time unit interval.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $unit;

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Sets time unit interval.
     *
     * @param string $unit
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setUnit($unit)
    {
        $validUnits = array(
            self::UNIT_HOUR,
            self::UNIT_DAY,
            self::UNIT_WEEK,
            self::UNIT_MONTH,
            self::UNIT_YEAR,
        );

        if (!in_array($unit, $validUnits)) {
            throw new \InvalidArgumentException('Unknown value for time unit interval.');
        }

        $this->unit = $unit;

        return $this;
    }

    /**
     * Gets time unit interval.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
