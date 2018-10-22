<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Console;

use Core\Console\Application;
use CoreTest\Bootstrap;
use Symfony\Component\Console\Tester\ApplicationTester;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $app = new Application(Bootstrap::getServiceManager());
        $app->setAutoExit(false);
        $tester = new ApplicationTester($app);
        $tester->run([], []);

        $output = $tester->getDisplay();
        $this->assertContains('YAWIK', $output);
    }
}
