<?php

/*
 * This file is part of the YAWIK project.
 *
 *     (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Controller\Console;

use Core\Controller\Console\AssetsInstallController;
use CoreTest\Bootstrap;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class AssetsInstallControllerTest extends AbstractConsoleControllerTestCase
{
    private $output;

    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getConfig());
        parent::setUp();
        $this->setUseConsoleRequest(true);
    }

    public function testSymlink()
    {
        $target = sys_get_temp_dir().'/yawik/assets-install';

        $this->dispatch('assets-install --symlink '.$target);

        $display = $this->getDisplay(true);

        $this->assertRegExp('/absolute symlink/', $display);
        $this->assertDirectoryExists($target.'/Core');
        $this->assertDirectoryExists($target.'/Applications');
    }

    public function testRelative()
    {
        $target = sys_get_temp_dir().'/yawik/assets-install';

        $this->dispatch('assets-install --relative '.$target);

        $display = $this->getDisplay(true);

        $this->assertRegExp('/relative symlink/', $display);
        $this->assertRegExp('/All assets were successfully installed/', $display);
        $this->assertDirectoryExists($target.'/Core');
        $this->assertDirectoryExists($target.'/Applications');
    }

    public function testCopy()
    {
        $target = sys_get_temp_dir().'/yawik/assets-install';

        $this->dispatch('assets-install '.$target);

        $display = $this->getDisplay(true);

        $this->assertRegExp('/Some assets were installed via copy/', $display);
        $this->assertDirectoryExists($target.'/Core');
        $this->assertDirectoryExists($target.'/Applications');
    }

    /**
     * Gets the display returned by the last execution of the command.
     *
     * @param bool $normalize Whether to normalize end of lines to \n or not
     *
     * @return string The display
     */
    public function getDisplay($normalize = false)
    {
        $sm = $this->getApplicationServiceLocator();
        $controller = $sm->get(AssetsInstallController::class);
        $output = $controller->getOutput();

        rewind($output->getStream());

        $display = stream_get_contents($output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }
}
