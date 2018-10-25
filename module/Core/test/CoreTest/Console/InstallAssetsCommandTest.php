<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Console;

/**
 * Class InstallAssetsCommandTest
 * @covers \Core\Console\InstallAssetsCommand
 * @package CoreTest\Console
 */
class InstallAssetsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $app = new TestApplication();
        $tester = $app->getCommandTester('assets:install');

        $path = sys_get_temp_dir().'/yawik/test/assets-install';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // test execute with hard copy
        $tester->execute(['target' =>$path]);
        $display = $tester->getDisplay(true);
        $this->assertContains('Some assets were installed via copy', $display);

        // test execute with relative options
        $tester->execute(['target' =>$path,'--relative' => true]);
        $display = $tester->getDisplay(true);
        $this->assertContains('relative symbolic links', $display);

        // test execute with absolute symlink options
        $tester->execute(['target' =>$path,'--symlink' => true]);
        $display = $tester->getDisplay(true);
        $this->assertContains('absolute symbolic links', $display);
    }
}
