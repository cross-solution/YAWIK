<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * Default options of the Core Module
 *
 * @package Core\Options
 */
class ModuleOptions extends AbstractOptions {

    /**
     * The sitename is used in Mails. Typically it's the name of your website
     *
     * @var string
     */
    protected $siteName="YAWIK";

    /**
     * Contact Data, which can be used in Mail signatures or the imprint page.
     *
     * @var array
     */
    protected $operator=array(
        'companyShortName'=>'Your Company Name',
        'companyFullName' => 'Your Company Name Ltd. & Co KG',
        'companyTax' => 'Your FAT Number',
        'postalCode' => 'xxxx',
        'city' => '',
        'name' => '',
        'email' => '',
        'fax' => ''
    );

    /**
     * @param $siteName
     * @return $this
     */
    public function setSiteName($siteName) {
        $this->siteName = $siteName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteName() {
        if (empty($this->siteName)) {
                throw new \InvalidArgumentException(
                    'the argument sitename has to be defined'
                );
        }
        return $this->siteName;
    }

    /**
     * @param $operator
     * @return $this
     */
    public function setOperator($operator) {
        $this->operator=$operator;
        return $this;
    }

    /**
     * @return array
     */
    public function getOperator() {
        return $this->operator;
    }


}