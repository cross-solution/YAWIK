<?php

/*
 * This file is part of the YAWIK project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Controller\Console;

use PHPUnit\Framework\TestCase;

use Core\Controller\Console\AssetsInstallController;
use CoreTest\Bootstrap;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Yawik\Composer\AssetsInstaller;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

/**
 * Class AssetsInstallControllerTest
 *
 * @package CoreTest\Controller\Console
 * @author  Anthonius Munthi <https://itstoni.com>
 * @since   0.32.0
 * @covers  \Core\Controller\Console\AssetsInstallController
 */
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

    protected function setUp(): void
    {
        /* @var AssetsInstallController $controller */
        $this->setApplicationConfig(Bootstrap::getConfig());
        $this->setUseConsoleRequest(true);
        $output = new StreamOutput(
            fopen('php://memory', 'w')
        );
        $manager = $this->getApplicationServiceLocator()->get('ControllerManager');
        $controller = $manager->get(AssetsInstallController::class);
        $controller->setOutput($output);
        $controller->setInput(new StringInput('some input'));

        $this->output       = $output;
        $this->controller   = $controller;
    }


    /**
     * @param   string  $option
     * @param string $expectedMethod
     * @dataProvider getTestInstallMethod
     * @throws \Exception
     */
    public function testInstallMethod($option, $expectedMethod)
    {
        $controller     = $this->controller;
        $installer      = $this->getMockBuilder(AssetsInstaller::class)
            ->setMethods(['install','setOutput','setInput'])
            ->getMock()
        ;
        $installer->expects($this->once())
            ->method('install')
            ->with($this->isType('array'), $expectedMethod)
        ;
        $installer->expects($this->once())
            ->method('setInput')
        ;
        $installer->expects($this->once())
            ->method('setOutput')
        ;

        $controller->setInstaller($installer);

        $this->dispatch("assets-install ".$option);
    }

    public function getTestInstallMethod()
    {
        return [
            ['--copy',      AssetsInstaller::METHOD_COPY],
            ['--relative',  AssetsInstaller::METHOD_RELATIVE_SYMLINK],
            ['--symlink',   AssetsInstaller::METHOD_ABSOLUTE_SYMLINK],
        ];
    }
}
