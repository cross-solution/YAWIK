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
use Symfony\Component\Console\Tester\CommandTester;

class TestApplication extends Application
{
    public function __construct()
    {
        parent::__construct(Bootstrap::getServiceManager());
        $this->setAutoExit(false);
    }

    /**
     * Get CommandTester
     * @param $commandName
     * @return CommandTester
     */
    public function getCommandTester($commandName)
    {
        $command = $this->find($commandName);
        return new CommandTester($command);
    }
}
