<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Install\Controller\Plugin\YawikConfigCreator;
use Install\Filter\DbNameExtractor;

/**
 * Tests for \Install\Controller\Plugin\YawikConfigCreator
 *
 * @covers \Install\Controller\Plugin\YawikConfigCreator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Controller
 * @group Install.Controller.Plugin
 * @since 0.20
 */
class YawikConfigCreatorTest extends TestCase
{
    /**
     * Class under test
     *
     * @var YawikConfigCreator
     */
    protected $target;

    protected function setUp(): void
    {
        $extractor = new DbNameExtractor('YAWIK.test');

        $this->target = new YawikConfigCreator($extractor);

        if (0 === strpos($this->getName(false), 'testExtends')) {
            return;
        }

        chdir(__DIR__);
    }

    public function testExtendsAbstractPlugin()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', $this->target);
    }

    public function testSettingConfigurationValues()
    {
        $result = $this->target->process('mongodb://server/TestDbName', 'test@email');

        $this->assertContains("'default_db' => 'TestDbName'", $result);
        $this->assertContains("'connectionString' => 'mongodb://server/TestDbName'", $result);
        $this->assertContains("'core_options'", $result);
        $this->assertContains("'system_message_email' => 'test@email'", $result);

        $result = $this->target->process('mongodb://server', 'test@email');

        $this->assertContains("'default_db' => 'YAWIK.test'", $result);
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
