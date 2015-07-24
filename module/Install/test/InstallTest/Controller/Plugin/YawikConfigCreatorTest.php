<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Controller\Plugin;

use Install\Controller\Plugin\YawikConfigCreator;

/**
 * Tests for \Install\Controller\Plugin\YawikConfigCreator
 *
 * @runTestsInSeparateProcesses
 * @covers \Install\Controller\Plugin\YawikConfigCreator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Controller
 * @group Install.Controller.Plugin
 */
class YawikConfigCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->target = new YawikConfigCreator();

        if (0 === strpos($this->getName(false), 'testExtends')) return;

        chdir(__DIR__);

    }

    public function testExtendsAbstractPlugin()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', new YawikConfigCreator());
    }

    public function testSettingConfigurationValues()
    {
        $result = $this->target->process('mongodb://server/TestDbName', 'test', 'pass');

        $this->assertContains("'default_db' => 'TestDbName'", $result);
        $this->assertContains("'default_user'", $result);
        $this->assertContains("'login' => 'test'", $result);
        $this->assertContains("'password' => 'pass'", $result);

        $result = $this->target->process('mongodb://server', 'user', 'pass');

        $this->assertContains("'default_db' => 'YAWIK'", $result);
    }

    public function testWritingConfigFileWorks()
    {
        $dir = sys_get_temp_dir() . '/YawikConfigCreator-' . uniqid();

        mkdir($dir);
        mkdir($dir . '/config');
        mkdir($dir . '/config/autoload');

        chdir($dir);

        $this->assertTrue($this->target->process('mongodb://server/dbname', 'user', 'pass'));

        $content = @file_get_contents("$dir/config/autoload/yawik.config.global.php");

        if ($content) {
            $this->assertContains("'default_db' => 'dbname'", $content);
            $this->assertContains("'default_user'", $content);
            $this->assertContains("'login' => 'user'", $content);
            $this->assertContains("'password' => 'pass'", $content);
        }

        unlink("$dir/config/autoload/yawik.config.global.php");
        rmdir("$dir/config/autoload");
        rmdir("$dir/config");
        rmdir($dir);

        if (!$content) {
            $this->fail('No file was created!');
        }

    }
}