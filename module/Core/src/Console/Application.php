<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Console;

use Core\Factory\ContainerAwareInterface;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\SplFileInfo;
use Zend\Mvc\Application as MvcApplication;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Finder\Finder;

/**
 * Class Application
 * @package Core\Console
 */
class Application extends BaseApplication
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Application constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $version = `git describe 2>/dev/null`;
        parent::__construct('YAWIK', $version);
        $this->container = $container;
        $this->registerCommands($container);
    }

    private function registerCommands(ContainerInterface $container)
    {
        /* @var \Zend\ModuleManager\ModuleManagerInterface $manager */
        $manager = $container->get('ModuleManager');
        $modules = $manager->getLoadedModules(true);
        foreach ($modules as $module) {
            $this->addModuleCommand($module);
        }
    }

    /**
     * @param $module
     */
    private function addModuleCommand($module)
    {
        $r = new \ReflectionObject($module);
        if (!$module instanceof ConsoleCommandProviderInterface) {
            return;
        }
        $module->registerCommands($this);
    }

    /**
     * @inheritdoc
     */
    public function add(Command $command)
    {
        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }
        return parent::add($command);
    }
}
