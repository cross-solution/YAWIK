<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Options;

use Core\Options\ModuleOptions as Options;

/**
 *
 * @covers \Core\Options\ModuleOptions
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Options
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    public function setUp()
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers Core\Options\ModuleOptions::getDefaultTaxRate
     * @covers Core\Options\ModuleOptions::setDefaultTaxRate
     */
    public function testSetGetDefaultTaxRate()
    {
        $this->options->setDefaultTaxRate(21);
        $this->assertEquals(21, $this->options->getDefaultTaxRate());
    }

    /**
     * @covers Core\Options\ModuleOptions::getDefaultLanguage
     * @covers Core\Options\ModuleOptions::setDefaultLanguage
     */
    public function testSetGetDefaultLanguage()
    {
        $this->options->setDefaultLanguage('de');
        $this->assertEquals('de', $this->options->getDefaultLanguage());
    }

    /**
     * @covers Core\Options\ModuleOptions::getDefaultCurrencyCode
     * @covers Core\Options\ModuleOptions::setDefaultCurrencyCode
     */
    public function testSetGetAttachmentsCount()
    {
        $this->options->setDefaultCurrencyCode("EUR");
        $this->assertEquals("EUR", $this->options->getDefaultCurrencyCode());
    }

    /**
     * @covers Core\Options\ModuleOptions::getOperator
     * @covers Core\Options\ModuleOptions::setOperator
     */
    public function testSetGetOperator()
    {
        $operator=array(
            'companyShortName'=>'my company',
            'companyFullName' => 'my company Ltd. & Co KG',
            'companyTax' => '12345',
            'postalCode' => '67890',
            'city' => 'Frankfurt',
            'street'=> 'Diemelstrasse 2-4',
            'name' => 'Carsten Bleek',
            'email' => 'name@example.de',
            'fax' => '0815/4711');

        $this->options->setOperator($operator);
        $this->assertEquals($operator, $this->options->getOperator());
    }

    /**
     * @covers Core\Options\ModuleOptions::getSiteName
     * @covers Core\Options\ModuleOptions::setSiteName
     */
    public function testSetGetSiteName()
    {
        $siteName="This Website";

        $this->options->setSiteName($siteName);
        $this->assertEquals($siteName, $this->options->getSiteName());
    }

    /**
     * @since 0.20
     */
    public function testGetSiteNameThrowsExceptionIfNotSet()
    {
        $this->setExpectedException(
             '\Core\Options\Exception\MissingOptionException',
             'Missing value for option "siteName"'
        );

        $this->options->setSiteName('');
        $this->options->getSiteName();

    }

    /**
     * @covers Core\Options\ModuleOptions::getSupportedLanguages
     * @covers Core\Options\ModuleOptions::setSupportedLanguages
     */
    public function testSetGetSupportedLanguages()
    {
        $supportedLanguages = array(
            'de' => 'de_DE',
            'fr' => 'fr',
            'es' => 'es',
            'it' => 'it');

        $this->options->setSupportedLanguages($supportedLanguages);
        $this->assertEquals($supportedLanguages, $this->options->getSupportedLanguages());
    }

    /**
     * @covers Core\Options\ModuleOptions::isDetectLanguage
     * @covers Core\Options\ModuleOptions::setDetectLanguage
     */
    public function testSetGetDetectLanguage()
    {
        $this->options->setDetectLanguage(true);
        $this->assertEquals(true, $this->options->isDetectLanguage());
    }

    /**
     * @since 0.20
     */
    public function testAllowsSettingAndGettingSystemMessageEmail()
    {
        $this->assertSame($this->options, $this->options->setSystemMessageEmail('test@mail'), 'Fluent interface broken');
        $this->assertEquals('test@mail', $this->options->getSystemMessageEmail());
    }

    /**
     * @since 0.20
     */
    public function testThrowsExceptionIfSystemMessageEmailIsNotSet()
    {
        $this->setExpectedException('\Core\Options\Exception\MissingOptionException', 'Missing value for option "systemMessageEmail"');

        $this->options->getSystemMessageEmail();
    }

}
