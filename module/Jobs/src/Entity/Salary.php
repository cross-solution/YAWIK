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
     * Salary time interval unit.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $unit;

    /**
     * Creates a new instance.
     *
     * @param float [$value] Amount value.
     * @param string [$currency] Currency code.
     * @param string [$unit] Salary time interval unit.
     *
     * @uses setValue()
     * @uses setCurrency()
     * @uses setUnit()
     * @throws \InvalidArgumentException if invalid values are passed.
     */
    public function __construct($value = null, $currency = null, $unit = null)
    {
        if (!is_null($value)) {
            $this->setValue($value);
        }

        if (!is_null($currency)) {
            $this->setCurrency($currency);
        }

        if (!is_null($unit)) {
            $this->setUnit($unit);
        }
    }

    /**
     * @param string $currency
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setCurrency($currency)
    {
        $validCurrencyCodes = self::getValidCurrencyCodes();

        if (!in_array($currency, $validCurrencyCodes)) {
            throw new \InvalidArgumentException('Unknown value for currency code.');
        }

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
     * Sets time interval unit.
     *
     * @param string $unit
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setUnit($unit)
    {
        $validUnits = self::getValidUnits();

        if (!in_array($unit, $validUnits)) {
            throw new \InvalidArgumentException('Unknown value for time unit interval.');
        }

        $this->unit = $unit;

        return $this;
    }

    /**
     * Gets time interval unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Gets valid time interval units collection.
     *
     * @return array
     */
    public static function getValidUnits()
    {
        return array(
            self::UNIT_HOUR,
            self::UNIT_DAY,
            self::UNIT_WEEK,
            self::UNIT_MONTH,
            self::UNIT_YEAR,
        );
    }

    /**
     * Gets valid currency codes.
     *
     * @return array
     */
    public static function getValidCurrencyCodes()
    {
        return array_keys(self::getValidCurrencies());
    }

    /**
     * Gets valid currencies collection.
     *
     * @return array
     */
    public static function getValidCurrencies()
    {
        return array(
            "USD" => array(
                "symbol" => "$",
                "name"  => /*@translate*/ "US Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "USD",
                "name_plural"  => /*@translate*/ "US dollars"
            ),
            "CAD" => array(
                "symbol" => "CA$",
                "name"  => /*@translate*/ "Canadian Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "CAD",
                "name_plural"  => /*@translate*/ "Canadian dollars"
            ),
            "EUR" => array(
                "symbol" => "€",
                "name"  => /*@translate*/ "Euro",
                "symbol_native" => "€",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "EUR",
                "name_plural"  => /*@translate*/ "euros"
            ),
            "AED" => array(
                "symbol" => "AED",
                "name"  => /*@translate*/ "United Arab Emirates Dirham",
                "symbol_native" => "د.إ.‏",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "AED",
                "name_plural"  => /*@translate*/ "UAE dirhams"
            ),
            "AFN" => array(
                "symbol" => "Af",
                "name"  => /*@translate*/ "Afghan Afghani",
                "symbol_native" => "؋",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "AFN",
                "name_plural"  => /*@translate*/ "Afghan Afghanis"
            ),
            "ALL" => array(
                "symbol" => "ALL",
                "name"  => /*@translate*/ "Albanian Lek",
                "symbol_native" => "Lek",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "ALL",
                "name_plural"  => /*@translate*/ "Albanian lekë"
            ),
            "AMD" => array(
                "symbol" => "AMD",
                "name"  => /*@translate*/ "Armenian Dram",
                "symbol_native" => "դր.",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "AMD",
                "name_plural"  => /*@translate*/ "Armenian drams"
            ),
            "ARS" => array(
                "symbol" => "AR$",
                "name"  => /*@translate*/ "Argentine Peso",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "ARS",
                "name_plural"  => /*@translate*/ "Argentine pesos"
            ),
            "AUD" => array(
                "symbol" => "AU$",
                "name"  => /*@translate*/ "Australian Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "AUD",
                "name_plural"  => /*@translate*/ "Australian dollars"
            ),
            "AZN" => array(
                "symbol" => "man.",
                "name"  => /*@translate*/ "Azerbaijani Manat",
                "symbol_native" => "ман.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "AZN",
                "name_plural"  => /*@translate*/ "Azerbaijani manats"
            ),
            "BAM" => array(
                "symbol" => "KM",
                "name"  => /*@translate*/ "Bosnia-Herzegovina Convertible Mark",
                "symbol_native" => "KM",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BAM",
                "name_plural"  => /*@translate*/ "Bosnia-Herzegovina convertible marks"
            ),
            "BDT" => array(
                "symbol" => "Tk",
                "name"  => /*@translate*/ "Bangladeshi Taka",
                "symbol_native" => "৳",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BDT",
                "name_plural"  => /*@translate*/ "Bangladeshi takas"
            ),
            "BGN" => array(
                "symbol" => "BGN",
                "name"  => /*@translate*/ "Bulgarian Lev",
                "symbol_native" => "лв.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BGN",
                "name_plural"  => /*@translate*/ "Bulgarian leva"
            ),
            "BHD" => array(
                "symbol" => "BD",
                "name"  => /*@translate*/ "Bahraini Dinar",
                "symbol_native" => "د.ب.‏",
                "decimal_digits" => 3,
                "rounding" => 0,
                "code" => "BHD",
                "name_plural"  => /*@translate*/ "Bahraini dinars"
            ),
            "BIF" => array(
                "symbol" => "FBu",
                "name"  => /*@translate*/ "Burundian Franc",
                "symbol_native" => "FBu",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "BIF",
                "name_plural"  => /*@translate*/ "Burundian francs"
            ),
            "BND" => array(
                "symbol" => "BN$",
                "name"  => /*@translate*/ "Brunei Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BND",
                "name_plural"  => /*@translate*/ "Brunei dollars"
            ),
            "BOB" => array(
                "symbol" => "Bs",
                "name"  => /*@translate*/ "Bolivian Boliviano",
                "symbol_native" => "Bs",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BOB",
                "name_plural"  => /*@translate*/ "Bolivian bolivianos"
            ),
            "BRL" => array(
                "symbol" => "R$",
                "name"  => /*@translate*/ "Brazilian Real",
                "symbol_native" => "R$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BRL",
                "name_plural"  => /*@translate*/ "Brazilian reals"
            ),
            "BWP" => array(
                "symbol" => "BWP",
                "name"  => /*@translate*/ "Botswanan Pula",
                "symbol_native" => "P",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BWP",
                "name_plural"  => /*@translate*/ "Botswanan pulas"
            ),
            "BYR" => array(
                "symbol" => "BYR",
                "name"  => /*@translate*/ "Belarusian Ruble",
                "symbol_native" => "BYR",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "BYR",
                "name_plural"  => /*@translate*/ "Belarusian rubles"
            ),
            "BZD" => array(
                "symbol" => "BZ$",
                "name"  => /*@translate*/ "Belize Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "BZD",
                "name_plural"  => /*@translate*/ "Belize dollars"
            ),
            "CDF" => array(
                "symbol" => "CDF",
                "name"  => /*@translate*/ "Congolese Franc",
                "symbol_native" => "FrCD",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "CDF",
                "name_plural"  => /*@translate*/ "Congolese francs"
            ),
            "CHF" => array(
                "symbol" => "CHF",
                "name"  => /*@translate*/ "Swiss Franc",
                "symbol_native" => "CHF",
                "decimal_digits" => 2,
                "rounding" => 0.05,
                "code" => "CHF",
                "name_plural"  => /*@translate*/ "Swiss francs"
            ),
            "CLP" => array(
                "symbol" => "CL$",
                "name"  => /*@translate*/ "Chilean Peso",
                "symbol_native" => "$",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "CLP",
                "name_plural"  => /*@translate*/ "Chilean pesos"
            ),
            "CNY" => array(
                "symbol" => "CN¥",
                "name"  => /*@translate*/ "Chinese Yuan",
                "symbol_native" => "CN¥",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "CNY",
                "name_plural"  => /*@translate*/ "Chinese yuan"
            ),
            "COP" => array(
                "symbol" => "CO$",
                "name"  => /*@translate*/ "Colombian Peso",
                "symbol_native" => "$",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "COP",
                "name_plural"  => /*@translate*/ "Colombian pesos"
            ),
            "CRC" => array(
                "symbol" => "₡",
                "name"  => /*@translate*/ "Costa Rican Colón",
                "symbol_native" => "₡",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "CRC",
                "name_plural"  => /*@translate*/ "Costa Rican colóns"
            ),
            "CVE" => array(
                "symbol" => "CV$",
                "name"  => /*@translate*/ "Cape Verdean Escudo",
                "symbol_native" => "CV$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "CVE",
                "name_plural"  => /*@translate*/ "Cape Verdean escudos"
            ),
            "CZK" => array(
                "symbol" => "Kč",
                "name"  => /*@translate*/ "Czech Republic Koruna",
                "symbol_native" => "Kč",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "CZK",
                "name_plural"  => /*@translate*/ "Czech Republic korunas"
            ),
            "DJF" => array(
                "symbol" => "Fdj",
                "name"  => /*@translate*/ "Djiboutian Franc",
                "symbol_native" => "Fdj",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "DJF",
                "name_plural"  => /*@translate*/ "Djiboutian francs"
            ),
            "DKK" => array(
                "symbol" => "Dkr",
                "name"  => /*@translate*/ "Danish Krone",
                "symbol_native" => "kr",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "DKK",
                "name_plural"  => /*@translate*/ "Danish kroner"
            ),
            "DOP" => array(
                "symbol" => "RD$",
                "name"  => /*@translate*/ "Dominican Peso",
                "symbol_native" => "RD$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "DOP",
                "name_plural"  => /*@translate*/ "Dominican pesos"
            ),
            "DZD" => array(
                "symbol" => "DA",
                "name"  => /*@translate*/ "Algerian Dinar",
                "symbol_native" => "د.ج.‏",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "DZD",
                "name_plural"  => /*@translate*/ "Algerian dinars"
            ),
            "EEK" => array(
                "symbol" => "Ekr",
                "name"  => /*@translate*/ "Estonian Kroon",
                "symbol_native" => "kr",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "EEK",
                "name_plural"  => /*@translate*/ "Estonian kroons"
            ),
            "EGP" => array(
                "symbol" => "EGP",
                "name"  => /*@translate*/ "Egyptian Pound",
                "symbol_native" => "ج.م.‏",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "EGP",
                "name_plural"  => /*@translate*/ "Egyptian pounds"
            ),
            "ERN" => array(
                "symbol" => "Nfk",
                "name"  => /*@translate*/ "Eritrean Nakfa",
                "symbol_native" => "Nfk",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "ERN",
                "name_plural"  => /*@translate*/ "Eritrean nakfas"
            ),
            "ETB" => array(
                "symbol" => "Br",
                "name"  => /*@translate*/ "Ethiopian Birr",
                "symbol_native" => "Br",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "ETB",
                "name_plural"  => /*@translate*/ "Ethiopian birrs"
            ),
            "GBP" => array(
                "symbol" => "£",
                "name"  => /*@translate*/ "British Pound Sterling",
                "symbol_native" => "£",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "GBP",
                "name_plural"  => /*@translate*/ "British pounds sterling"
            ),
            "GEL" => array(
                "symbol" => "GEL",
                "name"  => /*@translate*/ "Georgian Lari",
                "symbol_native" => "GEL",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "GEL",
                "name_plural"  => /*@translate*/ "Georgian laris"
            ),
            "GHS" => array(
                "symbol" => "GH₵",
                "name"  => /*@translate*/ "Ghanaian Cedi",
                "symbol_native" => "GH₵",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "GHS",
                "name_plural"  => /*@translate*/ "Ghanaian cedis"
            ),
            "GNF" => array(
                "symbol" => "FG",
                "name"  => /*@translate*/ "Guinean Franc",
                "symbol_native" => "FG",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "GNF",
                "name_plural"  => /*@translate*/ "Guinean francs"
            ),
            "GTQ" => array(
                "symbol" => "GTQ",
                "name"  => /*@translate*/ "Guatemalan Quetzal",
                "symbol_native" => "Q",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "GTQ",
                "name_plural"  => /*@translate*/ "Guatemalan quetzals"
            ),
            "HKD" => array(
                "symbol" => "HK$",
                "name"  => /*@translate*/ "Hong Kong Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "HKD",
                "name_plural"  => /*@translate*/ "Hong Kong dollars"
            ),
            "HNL" => array(
                "symbol" => "HNL",
                "name"  => /*@translate*/ "Honduran Lempira",
                "symbol_native" => "L",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "HNL",
                "name_plural"  => /*@translate*/ "Honduran lempiras"
            ),
            "HRK" => array(
                "symbol" => "kn",
                "name"  => /*@translate*/ "Croatian Kuna",
                "symbol_native" => "kn",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "HRK",
                "name_plural"  => /*@translate*/ "Croatian kunas"
            ),
            "HUF" => array(
                "symbol" => "Ft",
                "name"  => /*@translate*/ "Hungarian Forint",
                "symbol_native" => "Ft",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "HUF",
                "name_plural"  => /*@translate*/ "Hungarian forints"
            ),
            "IDR" => array(
                "symbol" => "Rp",
                "name"  => /*@translate*/ "Indonesian Rupiah",
                "symbol_native" => "Rp",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "IDR",
                "name_plural"  => /*@translate*/ "Indonesian rupiahs"
            ),
            "ILS" => array(
                "symbol" => "₪",
                "name"  => /*@translate*/ "Israeli New Sheqel",
                "symbol_native" => "₪",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "ILS",
                "name_plural"  => /*@translate*/ "Israeli new sheqels"
            ),
            "INR" => array(
                "symbol" => "Rs",
                "name"  => /*@translate*/ "Indian Rupee",
                "symbol_native" => "টকা",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "INR",
                "name_plural"  => /*@translate*/ "Indian rupees"
            ),
            "IQD" => array(
                "symbol" => "IQD",
                "name"  => /*@translate*/ "Iraqi Dinar",
                "symbol_native" => "د.ع.‏",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "IQD",
                "name_plural"  => /*@translate*/ "Iraqi dinars"
            ),
            "IRR" => array(
                "symbol" => "IRR",
                "name"  => /*@translate*/ "Iranian Rial",
                "symbol_native" => "﷼",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "IRR",
                "name_plural"  => /*@translate*/ "Iranian rials"
            ),
            "ISK" => array(
                "symbol" => "Ikr",
                "name"  => /*@translate*/ "Icelandic Króna",
                "symbol_native" => "kr",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "ISK",
                "name_plural"  => /*@translate*/ "Icelandic krónur"
            ),
            "JMD" => array(
                "symbol" => "J$",
                "name"  => /*@translate*/ "Jamaican Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "JMD",
                "name_plural"  => /*@translate*/ "Jamaican dollars"
            ),
            "JOD" => array(
                "symbol" => "JD",
                "name"  => /*@translate*/ "Jordanian Dinar",
                "symbol_native" => "د.أ.‏",
                "decimal_digits" => 3,
                "rounding" => 0,
                "code" => "JOD",
                "name_plural"  => /*@translate*/ "Jordanian dinars"
            ),
            "JPY" => array(
                "symbol" => "¥",
                "name"  => /*@translate*/ "Japanese Yen",
                "symbol_native" => "￥",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "JPY",
                "name_plural"  => /*@translate*/ "Japanese yen"
            ),
            "KES" => array(
                "symbol" => "Ksh",
                "name"  => /*@translate*/ "Kenyan Shilling",
                "symbol_native" => "Ksh",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "KES",
                "name_plural"  => /*@translate*/ "Kenyan shillings"
            ),
            "KHR" => array(
                "symbol" => "KHR",
                "name"  => /*@translate*/ "Cambodian Riel",
                "symbol_native" => "៛",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "KHR",
                "name_plural"  => /*@translate*/ "Cambodian riels"
            ),
            "KMF" => array(
                "symbol" => "CF",
                "name"  => /*@translate*/ "Comorian Franc",
                "symbol_native" => "FC",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "KMF",
                "name_plural"  => /*@translate*/ "Comorian francs"
            ),
            "KRW" => array(
                "symbol" => "₩",
                "name"  => /*@translate*/ "South Korean Won",
                "symbol_native" => "₩",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "KRW",
                "name_plural"  => /*@translate*/ "South Korean won"
            ),
            "KWD" => array(
                "symbol" => "KD",
                "name"  => /*@translate*/ "Kuwaiti Dinar",
                "symbol_native" => "د.ك.‏",
                "decimal_digits" => 3,
                "rounding" => 0,
                "code" => "KWD",
                "name_plural"  => /*@translate*/ "Kuwaiti dinars"
            ),
            "KZT" => array(
                "symbol" => "KZT",
                "name"  => /*@translate*/ "Kazakhstani Tenge",
                "symbol_native" => "тңг.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "KZT",
                "name_plural"  => /*@translate*/ "Kazakhstani tenges"
            ),
            "LBP" => array(
                "symbol" => "LB£",
                "name"  => /*@translate*/ "Lebanese Pound",
                "symbol_native" => "ل.ل.‏",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "LBP",
                "name_plural"  => /*@translate*/ "Lebanese pounds"
            ),
            "LKR" => array(
                "symbol" => "SLRs",
                "name"  => /*@translate*/ "Sri Lankan Rupee",
                "symbol_native" => "SL Re",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "LKR",
                "name_plural"  => /*@translate*/ "Sri Lankan rupees"
            ),
            "LTL" => array(
                "symbol" => "Lt",
                "name"  => /*@translate*/ "Lithuanian Litas",
                "symbol_native" => "Lt",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "LTL",
                "name_plural"  => /*@translate*/ "Lithuanian litai"
            ),
            "LVL" => array(
                "symbol" => "Ls",
                "name"  => /*@translate*/ "Latvian Lats",
                "symbol_native" => "Ls",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "LVL",
                "name_plural"  => /*@translate*/ "Latvian lati"
            ),
            "LYD" => array(
                "symbol" => "LD",
                "name"  => /*@translate*/ "Libyan Dinar",
                "symbol_native" => "د.ل.‏",
                "decimal_digits" => 3,
                "rounding" => 0,
                "code" => "LYD",
                "name_plural"  => /*@translate*/ "Libyan dinars"
            ),
            "MAD" => array(
                "symbol" => "MAD",
                "name"  => /*@translate*/ "Moroccan Dirham",
                "symbol_native" => "د.م.‏",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MAD",
                "name_plural"  => /*@translate*/ "Moroccan dirhams"
            ),
            "MDL" => array(
                "symbol" => "MDL",
                "name"  => /*@translate*/ "Moldovan Leu",
                "symbol_native" => "MDL",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MDL",
                "name_plural"  => /*@translate*/ "Moldovan lei"
            ),
            "MGA" => array(
                "symbol" => "MGA",
                "name"  => /*@translate*/ "Malagasy Ariary",
                "symbol_native" => "MGA",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "MGA",
                "name_plural"  => /*@translate*/ "Malagasy Ariaries"
            ),
            "MKD" => array(
                "symbol" => "MKD",
                "name"  => /*@translate*/ "Macedonian Denar",
                "symbol_native" => "MKD",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MKD",
                "name_plural"  => /*@translate*/ "Macedonian denari"
            ),
            "MMK" => array(
                "symbol" => "MMK",
                "name"  => /*@translate*/ "Myanma Kyat",
                "symbol_native" => "K",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "MMK",
                "name_plural"  => /*@translate*/ "Myanma kyats"
            ),
            "MOP" => array(
                "symbol" => "MOP$",
                "name"  => /*@translate*/ "Macanese Pataca",
                "symbol_native" => "MOP$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MOP",
                "name_plural"  => /*@translate*/ "Macanese patacas"
            ),
            "MUR" => array(
                "symbol" => "MURs",
                "name"  => /*@translate*/ "Mauritian Rupee",
                "symbol_native" => "MURs",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "MUR",
                "name_plural"  => /*@translate*/ "Mauritian rupees"
            ),
            "MXN" => array(
                "symbol" => "MX$",
                "name"  => /*@translate*/ "Mexican Peso",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MXN",
                "name_plural"  => /*@translate*/ "Mexican pesos"
            ),
            "MYR" => array(
                "symbol" => "RM",
                "name"  => /*@translate*/ "Malaysian Ringgit",
                "symbol_native" => "RM",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MYR",
                "name_plural"  => /*@translate*/ "Malaysian ringgits"
            ),
            "MZN" => array(
                "symbol" => "MTn",
                "name"  => /*@translate*/ "Mozambican Metical",
                "symbol_native" => "MTn",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "MZN",
                "name_plural"  => /*@translate*/ "Mozambican meticals"
            ),
            "NAD" => array(
                "symbol" => "N$",
                "name"  => /*@translate*/ "Namibian Dollar",
                "symbol_native" => "N$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "NAD",
                "name_plural"  => /*@translate*/ "Namibian dollars"
            ),
            "NGN" => array(
                "symbol" => "₦",
                "name"  => /*@translate*/ "Nigerian Naira",
                "symbol_native" => "₦",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "NGN",
                "name_plural"  => /*@translate*/ "Nigerian nairas"
            ),
            "NIO" => array(
                "symbol" => "C$",
                "name"  => /*@translate*/ "Nicaraguan Córdoba",
                "symbol_native" => "C$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "NIO",
                "name_plural"  => /*@translate*/ "Nicaraguan córdobas"
            ),
            "NOK" => array(
                "symbol" => "Nkr",
                "name"  => /*@translate*/ "Norwegian Krone",
                "symbol_native" => "kr",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "NOK",
                "name_plural"  => /*@translate*/ "Norwegian kroner"
            ),
            "NPR" => array(
                "symbol" => "NPRs",
                "name"  => /*@translate*/ "Nepalese Rupee",
                "symbol_native" => "नेरू",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "NPR",
                "name_plural"  => /*@translate*/ "Nepalese rupees"
            ),
            "NZD" => array(
                "symbol" => "NZ$",
                "name"  => /*@translate*/ "New Zealand Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "NZD",
                "name_plural"  => /*@translate*/ "New Zealand dollars"
            ),
            "OMR" => array(
                "symbol" => "OMR",
                "name"  => /*@translate*/ "Omani Rial",
                "symbol_native" => "ر.ع.‏",
                "decimal_digits" => 3,
                "rounding" => 0,
                "code" => "OMR",
                "name_plural"  => /*@translate*/ "Omani rials"
            ),
            "PAB" => array(
                "symbol" => "B/.",
                "name"  => /*@translate*/ "Panamanian Balboa",
                "symbol_native" => "B/.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "PAB",
                "name_plural"  => /*@translate*/ "Panamanian balboas"
            ),
            "PEN" => array(
                "symbol" => "S/.",
                "name"  => /*@translate*/ "Peruvian Nuevo Sol",
                "symbol_native" => "S/.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "PEN",
                "name_plural"  => /*@translate*/ "Peruvian nuevos soles"
            ),
            "PHP" => array(
                "symbol" => "₱",
                "name"  => /*@translate*/ "Philippine Peso",
                "symbol_native" => "₱",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "PHP",
                "name_plural"  => /*@translate*/ "Philippine pesos"
            ),
            "PKR" => array(
                "symbol" => "PKRs",
                "name"  => /*@translate*/ "Pakistani Rupee",
                "symbol_native" => "₨",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "PKR",
                "name_plural"  => /*@translate*/ "Pakistani rupees"
            ),
            "PLN" => array(
                "symbol" => "zł",
                "name"  => /*@translate*/ "Polish Zloty",
                "symbol_native" => "zł",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "PLN",
                "name_plural"  => /*@translate*/ "Polish zlotys"
            ),
            "PYG" => array(
                "symbol" => "₲",
                "name"  => /*@translate*/ "Paraguayan Guarani",
                "symbol_native" => "₲",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "PYG",
                "name_plural"  => /*@translate*/ "Paraguayan guaranis"
            ),
            "QAR" => array(
                "symbol" => "QR",
                "name"  => /*@translate*/ "Qatari Rial",
                "symbol_native" => "ر.ق.‏",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "QAR",
                "name_plural"  => /*@translate*/ "Qatari rials"
            ),
            "RON" => array(
                "symbol" => "RON",
                "name"  => /*@translate*/ "Romanian Leu",
                "symbol_native" => "RON",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "RON",
                "name_plural"  => /*@translate*/ "Romanian lei"
            ),
            "RSD" => array(
                "symbol" => "din.",
                "name"  => /*@translate*/ "Serbian Dinar",
                "symbol_native" => "дин.",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "RSD",
                "name_plural"  => /*@translate*/ "Serbian dinars"
            ),
            "RUB" => array(
                "symbol" => "RUB",
                "name"  => /*@translate*/ "Russian Ruble",
                "symbol_native" => "руб.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "RUB",
                "name_plural"  => /*@translate*/ "Russian rubles"
            ),
            "RWF" => array(
                "symbol" => "RWF",
                "name"  => /*@translate*/ "Rwandan Franc",
                "symbol_native" => "FR",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "RWF",
                "name_plural"  => /*@translate*/ "Rwandan francs"
            ),
            "SAR" => array(
                "symbol" => "SR",
                "name"  => /*@translate*/ "Saudi Riyal",
                "symbol_native" => "ر.س.‏",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "SAR",
                "name_plural"  => /*@translate*/ "Saudi riyals"
            ),
            "SDG" => array(
                "symbol" => "SDG",
                "name"  => /*@translate*/ "Sudanese Pound",
                "symbol_native" => "SDG",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "SDG",
                "name_plural"  => /*@translate*/ "Sudanese pounds"
            ),
            "SEK" => array(
                "symbol" => "Skr",
                "name"  => /*@translate*/ "Swedish Krona",
                "symbol_native" => "kr",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "SEK",
                "name_plural"  => /*@translate*/ "Swedish kronor"
            ),
            "SGD" => array(
                "symbol" => "S$",
                "name"  => /*@translate*/ "Singapore Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "SGD",
                "name_plural"  => /*@translate*/ "Singapore dollars"
            ),
            "SOS" => array(
                "symbol" => "Ssh",
                "name"  => /*@translate*/ "Somali Shilling",
                "symbol_native" => "Ssh",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "SOS",
                "name_plural"  => /*@translate*/ "Somali shillings"
            ),
            "SYP" => array(
                "symbol" => "SY£",
                "name"  => /*@translate*/ "Syrian Pound",
                "symbol_native" => "ل.س.‏",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "SYP",
                "name_plural"  => /*@translate*/ "Syrian pounds"
            ),
            "THB" => array(
                "symbol" => "฿",
                "name"  => /*@translate*/ "Thai Baht",
                "symbol_native" => "฿",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "THB",
                "name_plural"  => /*@translate*/ "Thai baht"
            ),
            "TND" => array(
                "symbol" => "DT",
                "name"  => /*@translate*/ "Tunisian Dinar",
                "symbol_native" => "د.ت.‏",
                "decimal_digits" => 3,
                "rounding" => 0,
                "code" => "TND",
                "name_plural"  => /*@translate*/ "Tunisian dinars"
            ),
            "TOP" => array(
                "symbol" => "T$",
                "name"  => /*@translate*/ "Tongan Paʻanga",
                "symbol_native" => "T$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "TOP",
                "name_plural"  => /*@translate*/ "Tongan paʻanga"
            ),
            "TRY" => array(
                "symbol" => "TL",
                "name"  => /*@translate*/ "Turkish Lira",
                "symbol_native" => "TL",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "TRY",
                "name_plural"  => /*@translate*/ "Turkish Lira"
            ),
            "TTD" => array(
                "symbol" => "TT$",
                "name"  => /*@translate*/ "Trinidad and Tobago Dollar",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "TTD",
                "name_plural"  => /*@translate*/ "Trinidad and Tobago dollars"
            ),
            "TWD" => array(
                "symbol" => "NT$",
                "name"  => /*@translate*/ "New Taiwan Dollar",
                "symbol_native" => "NT$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "TWD",
                "name_plural"  => /*@translate*/ "New Taiwan dollars"
            ),
            "TZS" => array(
                "symbol" => "TSh",
                "name"  => /*@translate*/ "Tanzanian Shilling",
                "symbol_native" => "TSh",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "TZS",
                "name_plural"  => /*@translate*/ "Tanzanian shillings"
            ),
            "UAH" => array(
                "symbol" => "₴",
                "name"  => /*@translate*/ "Ukrainian Hryvnia",
                "symbol_native" => "₴",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "UAH",
                "name_plural"  => /*@translate*/ "Ukrainian hryvnias"
            ),
            "UGX" => array(
                "symbol" => "USh",
                "name"  => /*@translate*/ "Ugandan Shilling",
                "symbol_native" => "USh",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "UGX",
                "name_plural"  => /*@translate*/ "Ugandan shillings"
            ),
            "UYU" => array(
                "symbol" => "\$U",
                "name"  => /*@translate*/ "Uruguayan Peso",
                "symbol_native" => "$",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "UYU",
                "name_plural"  => /*@translate*/ "Uruguayan pesos"
            ),
            "UZS" => array(
                "symbol" => "UZS",
                "name"  => /*@translate*/ "Uzbekistan Som",
                "symbol_native" => "UZS",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "UZS",
                "name_plural"  => /*@translate*/ "Uzbekistan som"
            ),
            "VEF" => array(
                "symbol" => "Bs.F.",
                "name"  => /*@translate*/ "Venezuelan Bolívar",
                "symbol_native" => "Bs.F.",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "VEF",
                "name_plural"  => /*@translate*/ "Venezuelan bolívars"
            ),
            "VND" => array(
                "symbol" => "₫",
                "name"  => /*@translate*/ "Vietnamese Dong",
                "symbol_native" => "₫",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "VND",
                "name_plural"  => /*@translate*/ "Vietnamese dong"
            ),
            "XAF" => array(
                "symbol" => "FCFA",
                "name"  => /*@translate*/ "CFA Franc BEAC",
                "symbol_native" => "FCFA",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "XAF",
                "name_plural"  => /*@translate*/ "CFA francs BEAC"
            ),
            "XOF" => array(
                "symbol" => "CFA",
                "name"  => /*@translate*/ "CFA Franc BCEAO",
                "symbol_native" => "CFA",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "XOF",
                "name_plural"  => /*@translate*/ "CFA francs BCEAO"
            ),
            "YER" => array(
                "symbol" => "YR",
                "name"  => /*@translate*/ "Yemeni Rial",
                "symbol_native" => "ر.ي.‏",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "YER",
                "name_plural"  => /*@translate*/ "Yemeni rials"
            ),
            "ZAR" => array(
                "symbol" => "R",
                "name"  => /*@translate*/ "South African Rand",
                "symbol_native" => "R",
                "decimal_digits" => 2,
                "rounding" => 0,
                "code" => "ZAR",
                "name_plural"  => /*@translate*/ "South African rand"
            ),
            "ZMK" => array(
                "symbol" => "ZK",
                "name"  => /*@translate*/ "Zambian Kwacha",
                "symbol_native" => "ZK",
                "decimal_digits" => 0,
                "rounding" => 0,
                "code" => "ZMK",
                "name_plural"  => /*@translate*/ "Zambian kwachas"
            )
        );
    }
}
