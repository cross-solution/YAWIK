<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ModuleOptions extends AbstractOptions
{

    /**
     * Currency (ISO 4217)
     *
     * @ODM\String
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * Currency symbol.
     *
     * @ODM\String
     * @var string
     */
    protected $currencySymbol = '€';

    /**
     * The tax rate in percent.
     *
     * @var float
     */
    protected $taxRate = 19;

    /**
     * @param string $currency
     *
     * @return self
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
     * @param string $currencySymbol
     *
     * @return self
     */
    public function setCurrencySymbol($currencySymbol)
    {
        $this->currencySymbol = $currencySymbol;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @param float $taxRate
     *
     * @return self
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }


    
}