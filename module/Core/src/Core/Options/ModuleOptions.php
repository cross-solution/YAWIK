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
     * @var array $operator
     */
    protected $operator=array(
        'companyShortName'=>'Your Company Name',
        'companyFullName' => 'Your Company Name Ltd. & Co KG',
        'companyTax' => 'Your FAT Number',
        'postalCode' => 'xxxx',
        'city' => '',
        'street'=> '',
        'name' => '',
        'email' => '',
        'fax' => ''
    );

    /**
     * This array defines the languages, which can be used.
     *
     * @var array $supportedLanguages
     */
    protected $supportedLanguages = array(
        'de' => 'de_DE',
        'fr' => 'fr',
        'en' => 'en',
        'es' => 'es',
        'it' => 'it',
    );

    /**
     * if true, YAWIK tries to detect the browser settings
     *
     * @var bool
     */
    protected $detectLanguage = true;

    /**
     * The default language is used, if no language is set
     *
     * @var string
     */
    protected $defaultLanguage = 'en';

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
     * Sets the operators contact data
     *
     * @param $operator
     * @return $this
     */
    public function setOperator($operator) {
        $this->operator=$operator;
        return $this;
    }

    /**
     * Gets the operators contact data
     *
     * @return array
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * Sets supported languages
     *
     * @param $supportedLanguages
     * @return $this
     */
    public function setSupportedLanguages($supportedLanguages) {
        $this->supportedLanguages=$supportedLanguages;
        return $this;
    }

    /**
     * Gets supported languages
     *
     * @return array
     */
    public function getSupportedLanguages() {
        return $this->supportedLanguages;
    }

    /**
     * Sets the default language
     *
     * @param $defaultLanguage
     * @return $this
     */
    public function setDefaultLanguage($defaultLanguage) {
        $this->defaultLanguage=$defaultLanguage;
        return $this;
    }

    /**
     * Gets the default languages
     *
     * @return string
     */
    public function getDefaultLanguage() {
        return $this->defaultLanguage;
    }

    /**
     * Enable or disable the detection of language setting of the browser
     *
     * @param $detectLanguage
     * @return $this
     */
    public function setDetectLanguage($detectLanguage) {
        $this->detectLanguage=$detectLanguage;
        return $this;
    }

    /**
     * Gets the browser language detecting setting
     *
     * @return string
     */
    public function getDetectLanguage() {
        return $this->detectLanguage;
    }
}