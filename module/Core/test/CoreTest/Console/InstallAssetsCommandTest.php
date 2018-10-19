<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Console;

class InstallAssetsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $app = new TestApplication();
        $tester = $app->getCommandTester('assets:install');

        // test execute with hard copy
        $tester->execute([]);
        $display = $tester->getDisplay(true);
        $this->assertContains('Some assets were installed via copy', $display);

        // test execute with relative options
        $tester->execute(['--relative' => true]);
        $display = $tester->getDisplay(true);
        $this->assertContains('relative symbolic links', $display);

        // test execute with absolute symlink options
        $tester->execute(['--symlink' => true]);
        $display = $tester->getDisplay(true);
        $this->assertContains('absolute symbolic links', $display);
    }
}
