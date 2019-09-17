<?php

/*
 * This file is part of the YAWIK project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ReleaseToolsTest\Console;

use ReleaseTools\Console\SubsplitController;
use CoreTest\Bootstrap;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class SubsplitControllerTest extends AbstractConsoleControllerTestCase
{
    /**
     * @var SubsplitController
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

        $manager = $this->getApplicationServiceLocator()->get('ControllerManager');
        $controller = $manager->get(SubsplitController::class);
        $output = new StreamOutput(fopen('php://memory', 'w'));
        $controller->setOutput($output);
        $this->output = $output;
        $this->controller = $controller;
    }

    public function testProcessAll()
    {
        $this->dispatch('subsplit --dry-run -v');
        $display = $this->getDisplay();

        $this->assertRegExp('/Processing Applications module/', $display);
        $this->assertRegExp('/Processing Behat module/', $display);
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
