<?php

/*
 * This file is part of the YAWIK project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\Controller\Console;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Yawik\Composer\AssetsInstaller;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

/**
 * Class AssetsInstallController
 *
 * @package     Core\Controller\Console
 * @author      Anthonius Munthi <me@itstoni.com>
 * @since       0.32.0
 */
class AssetsInstallController extends AbstractConsoleController
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * An array list of injected modules
     * @var array
     */
    private $modules = [];

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var AssetsInstaller
     */
    protected $installer;

    /**
     * AssetsInstallController constructor.
     * @param array $modules List of installed modules
     */
    public function __construct(array $modules = [])
    {
        $this->modules      = $modules;
        $this->filesystem   = new Filesystem();
        $this->input        = new ArgvInput();
        $this->output       = new ConsoleOutput();
        $this->installer    = new AssetsInstaller();
    }

    /**
     * Creates new object
     * @param   ContainerInterface $container
     * @return AssetsInstallController
     */
    public static function factory(ContainerInterface $container)
    {
        /* @var ModuleManager $manager */
        $manager = $container->get('ModuleManager');
        $modules = $manager->getLoadedModules();

        return new static($modules);
    }

    /**
     * @param InputInterface $input
     * @return AssetsInstallController
     */
    public function setInput($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @param OutputInterface $output
     * @return AssetsInstallController
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    public function setInstaller(AssetsInstaller $installer)
    {
        $this->installer = $installer;
    }

    public function indexAction()
    {
        /* @var \Zend\Console\Request $request */
        $modules        = $this->modules;
        $request        = $this->getRequest();
        $symlink        = $request->getParam('symlink');
        $relative       = $request->getParam('relative');
        $installer      = $this->installer;
        $assets         = $installer->getModulesAsset($modules);

        // setup expected method
        if ($relative) {
            $expectedMethod = AssetsInstaller::METHOD_RELATIVE_SYMLINK;
        } elseif ($symlink) {
            $expectedMethod = AssetsInstaller::METHOD_ABSOLUTE_SYMLINK;
        } else {
            $expectedMethod = AssetsInstaller::METHOD_COPY;
        }

        $installer->setInput($this->input);
        $installer->setOutput($this->output);
        $installer->install($assets, $expectedMethod);
    }
}
