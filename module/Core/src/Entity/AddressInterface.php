<?php
/**
 * YAWIK
 * Application configuration
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Entity;

use Core\Entity\EntityInterface;

interface AddressInterface extends EntityInterface
{
       
   /**
    * Postal Code of the Address.
    *
    * @param string $postalCode
    * @return AddressInterface
    */
    public function setPostalCode($postalCode);
    public function getPostalCode();
   
    /**
     * Identifies the town or the city
     *
     * @param string $cityName
     * @return AddressInterface
     */
    public function setCityName($cityName);
    public function getCityName();

    /**
     * The Street Name where the building/ house is located
     *
     * @param string $streetName
     * @return AddressInterface
     */
    public function setStreetName($streetName);
    public function getStreetName();

    /**
     * The Number of the building or house on the street that identifies where
     * to deliver mail. For example, Building 300 on Standards Parkway
     *
     * @param string $buildingNumber
     * @return AddressInterface
     */
    public function setBuildingNumber($buildingNumber);
    public function getBuildingNumber();
    
    /**
     * Two-letter codes from the ISO 3166 standard as implemented by The Internet Assigned
     * Numbers Authority.
     *
     * @param string $postalCode {'AC'|'AD'|'AE'|'AF'|'AG'|'AI'|'AL'|'AM'|'AN'|'AO'|'AQ'|'AR'|'AS'|'AT'|'AU'|'AW'|'AX'|'AZ'|'BA'|'BB'|'BD'|'BE'|'BF'|'BG'|'BH'|'BI'|'BJ'|'BL'|'BM'|'BN'|'BO'|'BR'|'BS'|'BT'|'BV'|'BW'|'BY'|'BZ'|'CA'|'CC'|'CD'|'CF'|'CG'|'CH'|'CI'|'CK'|'CL'|'CM'|'CN'|'CO'|'CR'|'CU'|'CV'|'CX'|'CY'|'CZ'|'DE'|'DJ'|'DK'|'DM'|'DO'|'DZ'|'EC'|'EE'|'EG'|'EH'|'ER'|'ES'|'ET'|'EU'|'FI'|'FJ'|'FK'|'FM'|'FO'|'FR'|'GA'|'GB'|'GD'|'GE'|'GF'|'GG'|'GH'|'GI'|'GL'|'GM'|'GN'|'GP'|'GQ'|'GR'|'GS'|'GT'|'GU'|'GW'|'GY'|'HK'|'HM'|'HN'|'HR'|'HT'|'HU'|'ID'|'IE'|'IL'|'IM'|'IN'|'IO'|'IQ'|'IR'|'IS'|'IT'|'JE'|'JM'|'JO'|'JP'|'KE'|'KG'|'KH'|'KI'|'KM'|'KN'|'KP'|'KR'|'KW'|'KY'|'KZ'|'LA'|'LB'|'LC'|'LI'|'LK'|'LR'|'LS'|'LT'|'LU'|'LV'|'LY'|'MA'|'MC'|'MD'|'ME'|'MF'|'MG'|'MH'|'MK'|'ML'|'MM'|'MN'|'MO'|'MP'|'MQ'|'MR'|'MS'|'MT'|'MU'|'MV'|'MW'|'MX'|'MY'|'MZ'|'NA'|'NC'|'NE'|'NF'|'NG'|'NI'|'NL'|'NO'|'NP'|'NR'|'NU'|'NZ'|'OM'|'PA'|'PE'|'PF'|'PG'|'PH'|'PK'|'PL'|'PM'|'PN'|'PR'|'PS'|'PT'|'PW'|'PY'|'QA'|'RE'|'RO'|'RS'|'RU'|'RW'|'SA'|'SB'|'SC'|'SD'|'SE'|'SG'|'SH'|'SI'|'SJ'|'SK'|'SL'|'SM'|'SN'|'SO'|'SR'|'ST'|'SU'|'SV'|'SY'|'SZ'|'TC'|'TD'|'TF'|'TG'|'TH'|'TJ'|'TK'|'TL'|'TM'|'TN'|'TO'|'TP'|'TR'|'TT'|'TV'|'TW'|'TZ'|'UA'|'UG'|'UK'|'UM'|'US'|'UY'|'UZ'|'VA'|'VC'|'VE'|'VG'|'VI'|'VN'|'VU'|'WF'|'WS'|'YE'|'YT'|'YU'|'ZA'|'ZM'|'ZW'}
     * @return AddressInterface
     */
    public function setCountryCode($countryCode);
    public function getCountryCode();
}
