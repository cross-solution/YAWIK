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

    static private $configFile;

    static private $yawikGlobalConfig;

    static private $yawikBackupConfig;

    public function __construct()
    {
        static::$configFile = getcwd() . '/config/autoload/install.module.php';
        static::$yawikGlobalConfig = getcwd() . '/config/autoload/yawik.config.global.php';
        static::$yawikBackupConfig = str_replace('yawik.config.global.php', 'yawik.backup', static::$yawikGlobalConfig);
    }

    /**
     * @Given I have install module activated
     */
    public function iHaveInstallModuleActivated()
    {
        $target = static::$configFile;
        if(!file_exists($target)){
            $source = __DIR__.'/../resources/install.module.php';
            copy($source,$target);
            chmod($target,0777);
        }

        // backup existing file
        $yawikBackupConfig = static::$yawikBackupConfig;
        $yawikGlobalConfig = static::$yawikGlobalConfig;

        if(is_file($yawikGlobalConfig)){
            rename($yawikGlobalConfig,$yawikBackupConfig);
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
    static public function tearDown()
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
    static public function setUp()
    {
        static::restoreConfig();
    }

    static public function restoreConfig()
    {
        if(is_file($file = static::$configFile)){
            unlink($file);
        }

        // restore backup
        $yawikBackupConfig = static::$yawikBackupConfig;
        $yawikGlobalConfig = static::$yawikGlobalConfig;

        if(is_file($yawikBackupConfig)){
            rename($yawikBackupConfig,$yawikGlobalConfig);
        }
    }

    /**
     * @Given I fill database connection with an active connection
     */
    public function iFillActiveConnectionString()
    {
        $config = $this->getService('config');
        $connection = $config['doctrine']['connection']['odm_default']['connectionString'];
        $this->minkContext->fillField('db_conn',$connection);
    }
}
