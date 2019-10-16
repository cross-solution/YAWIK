<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\ModuleManager\Feature {

use Core\ModuleManager\Feature\VersionProviderTrait;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Core\ModuleManager\Feature\VersionProviderTrait
 * 
 * @covers \Core\ModuleManager\Feature\VersionProviderTrait
 * @runTestsInSeparateProcesses
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class VersionProviderTraitTest extends TestCase
{
    public function testTraitUsesConstants()
    {
        \putenv('APPLICATION_ENV=production');
        $target = new VersionProviderWithConstants();

        $this->assertEquals(VersionProviderWithConstants::NAME, $target->getName());
        $this->assertEquals(VersionProviderWithConstants::VERSION, $target->getVersion());
    }

    public function testTraitWithoutConstants()
    {
        \putenv('APPLICATION_ENV=production');
        $target = new VersionProviderWithoutConstants();

        $this->assertEquals('n/a', $target->getVersion());
        $this->assertEquals(__NAMESPACE__, $target->getName());

        $this->assertEquals('fancy/module', $target->getName());

        $this->assertEquals(__NAMESPACE__, $target->getName());

    }

    public function testGetVersionRunsGitCommands()
    {
        $version = (new VersionProviderWithConstants())->getVersion();

        $this->assertEquals('v1-1@1234567 [branch]', $version);

        $this->assertEquals('v2-2@7654321', (new VersionProviderWithConstants())->getVersion());

        $this->assertEquals('v1', (new VersionProviderWithConstants())->getVersion());

        $this->assertEquals('n/a', (new VersionProviderWithoutConstants())->getVersion());
    }
}

class VersionProviderWithoutConstants {
    use VersionProviderTrait;
}

class VersionProviderWithConstants {
    use VersionProviderTrait;

    const NAME='fancy/module';
    const VERSION = 'v1';
}

}

namespace Core\ModuleManager\Feature {
    function exec($command, &$output)
    {
        static $i = 0;

        switch ($i) {
            case 0:

                $output[] = 'v1-1-g1234567';
                $output[] = 'branch';
                break;

            case 1:
                $output[] = 'v2-2-g7654321';

                break;

            default:
                break;
        }
        $i+=1;
    }

    function dirname($str) { if (false !== strpos($str, '.php')) { return \dirname($str); } return $str; }

    function file_exists($name) {
        static $i = 0;

        return 0 != $i++;
    }

    function file_get_contents($name) {
        static $i = 0;

        if (0 == $i++) {
            return '{"name":"fancy/module"}';
        }

        return '{"noNameHere":"howsad"}';
    }
}
