<?php

/*
 * This file is part of the YAWIK project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Controller\Console;

use Core\Controller\Console\AssetsInstallController;
use CoreTest\Bootstrap;
use Symfony\Component\Console\Output\StreamOutput;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class AssetsInstallControllerTest extends AbstractConsoleControllerTestCase
{
    /**
     * @var AssetsInstallController
     */
    private $controller;

    /**
     * @var StreamOutput
     */
    private $output;

    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getConfig());
        parent::setUp();
        $this->setUseConsoleRequest(true);

        $output = new StreamOutput(
            fopen('php://memory', 'w')
        );
        $manager = $this->getApplicationServiceLocator()->get('ControllerManager');
        $controller = $manager->get(AssetsInstallController::class);
        $controller->setOutput($output);
        $this->output = $output;
        $this->controller = $controller;
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
        $output = $this->output;

        rewind($output->getStream());

        $display = stream_get_contents($output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }
}
