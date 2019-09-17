<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Options;

use PHPUnit\Framework\TestCase;

use Core\Application;
use Core\Options\ModuleOptions as Options;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 *
 * @covers \Core\Options\ModuleOptions
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <https://itstoni.com>
 * @group Core
 * @group Core.Options
 */
class ModuleOptionsTest extends TestCase
{
    use TestSetterGetterTrait, SetupTargetTrait;

    /**
     * @var Options
     */
    protected $target = [
        'class' => Options::class
    ];

    /**
     * @var Options $options
     */
    protected $options;

    public function propertiesProvider()
    {
        return [
            ['siteLogo', [
                'value' => 'some-logo.jpg',
                'default' => 'modules/Core/images/logo.jpg'
            ]],
            ['siteName', [
                'value' => 'MyName',
                'default' => 'YAWIK'
            ]],
            ['defaultTaxRate', [
                'value' => '21',
                'default' => '19'
            ]],
            ['defaultLanguage', [
                'value' => 'fr',
                'default' => 'en'
            ]],
            ['defaultCurrencyCode', [
                'value' => 'EUR',
                'default' => 'USD'
            ]],
            ['operator', [
                'value' => [
                    'companyShortName' => 'My Company',
                    'companyFullName'  => 'My Co KG',
                    'companyTax'       => 'My VAT Number',
                    'postalCode'       => 'MY Zip',
                    'city'             => 'My Cits',
                    'country'          => 'My country',
                    'street'           => 'MY Rath Dínen 112',
                    'name'             => 'MY Gimli & Legolas',
                    'email'            => 'me@example.com',
                    'fax'              => '+49-0815',
                    'homepage'         => 'https://example.com'],
                'default' =>  [
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
                    'homepage'         => 'http://example.com']
            ]],

            ['defaultCurrencyCode', [
                'value' => 'EUR',
                'default' => 'USD'
            ]],
        ];
    }


    /**
     * @since 0.20
     */
    public function testGetSiteNameThrowsExceptionIfNotSet()
    {
        $this->expectException('\Core\Options\Exception\MissingOptionException');
        $this->expectExceptionMessage('Missing value for option "siteName"');

        $this->target->setSiteName('');
        $this->target->getSiteName();
    }

    /**
     * @covers \Core\Options\ModuleOptions::getSupportedLanguages
     * @covers \Core\Options\ModuleOptions::setSupportedLanguages
     */
    public function testSetGetSupportedLanguages()
    {
        $supportedLanguages = array(
            'de' => 'de_DE',
            'fr' => 'fr',
            'es' => 'es',
            'it' => 'it');

        $this->target->setSupportedLanguages($supportedLanguages);
        $this->assertEquals($supportedLanguages, $this->target->getSupportedLanguages());
    }

    /**
     * @covers \Core\Options\ModuleOptions::isDetectLanguage
     * @covers \Core\Options\ModuleOptions::setDetectLanguage
     */
    public function testSetGetDetectLanguage()
    {
        $this->target->setDetectLanguage(true);
        $this->assertEquals(true, $this->target->isDetectLanguage());
    }

    /**
     * @since 0.20
     */
    public function testAllowsSettingAndGettingSystemMessageEmail()
    {
        $this->assertSame($this->target, $this->target->setSystemMessageEmail('test@mail'), 'Fluent interface broken');
        $this->assertEquals('test@mail', $this->target->getSystemMessageEmail());
    }

    /**
     * @since 0.20
     */
    public function testThrowsExceptionIfSystemMessageEmailIsNotSet()
    {
        $this->expectException('\Core\Options\Exception\MissingOptionException');
        $this->expectExceptionMessage('Missing value for option "systemMessageEmail"');

        $this->target->getSystemMessageEmail();
    }

    public function testLogDir()
    {
        $target = $this->target;
        $dir1   = getcwd().'/var/log';
        $dir2   = sys_get_temp_dir().'/yawik/some-log-dir';

        $this->assertEquals($dir1, $target->getLogDir());

        $target->setLogDir($dir2);
        $this->assertEquals($dir2, $target->getLogDir());
        $this->assertDirectoryExists($dir2, $target->getLogDir());
    }

    public function testConfigDir()
    {
        $this->assertEquals(Application::getConfigDir(), $this->target->getConfigDir());
    }

    public function testCacheDir()
    {
        $target = $this->target;
        $dir1   = getcwd().'/var/cache';
        $dir2   = sys_get_temp_dir().'/yawik/some-cache-dir';

        $this->assertEquals($dir1, $target->getCacheDir());

        $target->setCacheDir($dir2);
        $this->assertEquals($dir2, $target->getCacheDir());
        $this->assertDirectoryExists($dir2);
    }
}
