<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

/**
 * Class InstallContext
 *
 * @package Yawik\Behat
 * @author Anthonius Munthi <me@itstoni.com>
 */
class InstallContext implements Context
{
    use CommonContextTrait;

    private static $yawikGlobalConfig;

    private static $yawikBackupConfig;

    public function __construct()
    {
        static::$yawikGlobalConfig = getcwd() . '/config/autoload/yawik.config.global.php';
        static::$yawikBackupConfig = str_replace('yawik.config.global.php', 'yawik.backup', static::$yawikGlobalConfig);
    }

    /**
     * @Given I have install module activated
     */
    public function iHaveInstallModuleActivated()
    {
        // backup existing file
        $yawikBackupConfig = static::$yawikBackupConfig;
        $yawikGlobalConfig = static::$yawikGlobalConfig;

        if (is_file($yawikGlobalConfig)) {
            rename($yawikGlobalConfig, $yawikBackupConfig);
        }
    }

    /**
     * @Given I go to the install page
     */
    public function iGoToInstallPage()
    {
        $url = $this->minkContext->locatePath('/');
        $this->visit($url);
    }

    /**
     * @AfterSuite
     */
    public static function tearDown()
    {
        static::restoreConfig();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        static::restoreConfig();
    }

    /**
     * @BeforeSuite
     */
    public static function setUp()
    {
        static::restoreConfig();
    }

    public static function restoreConfig()
    {
        // restore backup
        $yawikBackupConfig = static::$yawikBackupConfig;
        $yawikGlobalConfig = static::$yawikGlobalConfig;

        if (is_file($yawikBackupConfig)) {
            rename($yawikBackupConfig, $yawikGlobalConfig);
        }
    }

    /**
     * @Given I fill database connection with an active connection
     */
    public function iFillActiveConnectionString()
    {
        $config = $this->getService('config');
        $connection = $config['doctrine']['connection']['odm_default']['connectionString'];
        $this->minkContext->fillField('db_conn', $connection);
    }
}
