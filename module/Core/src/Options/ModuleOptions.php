<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 * @author        weitz@cross-solution.de
 */

namespace Core\Options;

use Core\Application;
use Core\Options\Exception\MissingOptionException;
use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * Default options of the Core Module
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <https://itstoni.com>
 */
class ModuleOptions extends AbstractOptions
{

    /**
     * The sitename is used in Mails. Typically it's the name of your website
     *
     * @var string
     */
    protected $siteName = "YAWIK";

    /**
     * The logo of the site
     *
     * @var string
     */
    protected $siteLogo = "modules/Core/images/logo.jpg";

    /**
     * Contact Data, which can be used in Mail signatures or the imprint page.
     *
     * @var array $operator
     */
    protected $operator = array(
        'companyShortName' => 'Example Company Name',
        'companyFullName'  => 'Example Company Name Ltd. & Co KG',
        'companyTax'       => 'Your VAT Number',
        'postalCode'       => '4711',
        'city'             => 'Froschmoorstetten',
        'country'          => 'Auenland',
        'street'           => 'Rath Dínen 112',
        'name'             => 'Gimli & Legolas',
        'email'            => 'name@example.com',
        'fax'              => '+49-0815-4711',
        'homepage'         => 'http://example.com'
    );

    /**
     * This array defines the languages, which can be used.
     *
     * @var array $supportedLanguages
     */
    protected $supportedLanguages = array(
        'de' => 'de_DE',
        'fr' => 'fr',
        'en' => 'en_US',
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
     * default currency used, if no currency is set
     *
     * @var string
     */
    protected $defaultCurrencyCode = "USD";

    /**
     * default tax rate used, if no tax rate is set
     *
     * @var string
     */
    protected $defaultTaxRate = "19";

    /**
     * Email address to send system messages to.
     *
     * @var string
     */
    protected $systemMessageEmail;

    /**
     * Cache directory location
     * @var string
     */
    protected $cacheDir;

    /**
     * Public directory location
     * @var string
     */
    protected $publicDir;

    /**
     * Public directory location
     * @var string
     */
    protected $logDir;

    /**
     * @return string
     * @throws MissingOptionException
     * @since 0.20 throws MissingOptionException instead of InvalidArgumentException
     */
    public function getSiteName()
    {
        if (empty($this->siteName)) {
            throw new MissingOptionException('siteName', $this);
        }

        return $this->siteName;
    }

    /**
     * @param $siteName
     *
     * @return $this
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteLogo()
    {
        return $this->siteLogo;
    }

    /**
     * @param $siteLogo
     *
     * @return $this
     */
    public function setSiteLogo($siteLogo)
    {
        $this->siteLogo = $siteLogo;
        return $this;
    }


    /**
     * Gets the operators contact data
     *
     * @return array
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Sets the operators contact data
     *
     * @param $operator
     *
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Gets supported languages
     *
     * @return array
     */
    public function getSupportedLanguages()
    {
        return $this->supportedLanguages;
    }

    /**
     * Sets supported languages
     *
     * @param $supportedLanguages
     *
     * @return $this
     */
    public function setSupportedLanguages($supportedLanguages)
    {
        $this->supportedLanguages = $supportedLanguages;

        return $this;
    }

    /**
     * Gets the default languages
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Sets the default language
     *
     * @param $defaultLanguage
     *
     * @return $this
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * Enable or disable the detection of language setting of the browser
     *
     * @param $detectLanguage
     *
     * @return $this
     */
    public function setDetectLanguage($detectLanguage)
    {
        $this->detectLanguage = $detectLanguage;

        return $this;
    }

    /**
     * Gets the browser language detecting setting
     *
     * @return bool
     */
    public function isDetectLanguage()
    {
        return $this->detectLanguage;
    }

    /**
     * Gets the default languages
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->defaultCurrencyCode;
    }

    /**
     * Sets the default language
     *
     * @param $defaultCurrency
     *
     * @return $this
     */
    public function setDefaultCurrencyCode($defaultCurrency)
    {
        $this->defaultCurrencyCode = $defaultCurrency;

        return $this;
    }

    /**
     * Gets the default tax rate
     *
     * @return string
     */
    public function getDefaultTaxRate()
    {
        return $this->defaultTaxRate;
    }

    /**
     * Sets the default tax rate
     *
     * @param $defaultTaxRate
     *
     * @return $this
     */
    public function setDefaultTaxRate($defaultTaxRate)
    {
        $this->defaultTaxRate = $defaultTaxRate;

        return $this;
    }

    /**
     * Gets system message email address.
     *
     * @return string
     * @throws MissingOptionException if no value is set.
     * @since 0.20
     */
    public function getSystemMessageEmail()
    {
        if (!$this->systemMessageEmail) {
            throw new MissingOptionException('systemMessageEmail', $this);
        }
        return $this->systemMessageEmail;
    }

    /**
     * Sets system message email address.
     *
     * @param string $systemMessageEmail
     *
     * @return self
     * @since 0.20
     */
    public function setSystemMessageEmail($systemMessageEmail)
    {
        $this->systemMessageEmail = $systemMessageEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        if (is_null($this->cacheDir)) {
            $this->setCacheDir(getcwd().'/var/cache');
        }

        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return string
     */
    public function getPublicDir()
    {
        if (is_null($this->publicDir)) {
            $this->setPublicDir(getcwd().DIRECTORY_SEPARATOR.'public');
        }
        return $this->publicDir;
    }

    /**
     * @param string $publicDir
     * @return ModuleOptions
     */
    public function setPublicDir($publicDir)
    {
        $this->publicDir = $publicDir;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        if (is_null($this->logDir)) {
            $this->setLogDir(getcwd().'/var/log');
        }
        return $this->logDir;
    }

    /**
     * @param string $logDir
     * @return ModuleOptions
     */
    public function setLogDir($logDir)
    {
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $this->logDir = $logDir;

        return $this;
    }

    public function getConfigDir()
    {
        return Application::getConfigDir();
    }
}
