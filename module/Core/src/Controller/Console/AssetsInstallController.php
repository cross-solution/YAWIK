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
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Zend\View\Stream;

/**
 * Class AssetsInstallController
 *
 * @package     Core\Controller\Console
 * @author      Anthonius Munthi <me@itstoni.com>
 * @since       0.32.0
 */
class AssetsInstallController extends AbstractConsoleController
{
    const METHOD_COPY               = 'copy';
    const METHOD_ABSOLUTE_SYMLINK   = 'absolute symlink';
    const METHOD_RELATIVE_SYMLINK   = 'relative symlink';

    /**
     * @var Filesystem
     */
    private $filesystem;

    private $assets = [];

    private $modules = [];

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(array $modules = [])
    {
        $this->modules = $modules;
        $this->filesystem = new Filesystem();
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
    }

    public static function factory(ContainerInterface $container)
    {
        static $stream;

        /* @var ModuleManager $manager */
        $manager = $container->get('ModuleManager');

        $modules = $manager->getLoadedModules();
        $config = $container->get('ApplicationConfig');
        $ob = new static($modules);

        if ($config['environment'] === 'test') {
            if (is_null($stream)) {
                $stream = fopen('php://memory', 'w');
            }
            $ob->setOutput(new StreamOutput(
                $stream
            ));
        }
        return $ob;
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

    public function getOutput()
    {
        return $this->output;
    }

    public function indexAction()
    {
        /* @var \Zend\Console\Request $request */


        $modules = $this->modules;
        foreach ($modules as $module) {
            $this->processModule($module);
        }

        $io = new SymfonyStyle($this->input, $this->output);
        $io->newLine();

        $rows = [];
        $exitCode = 0;
        $copyUsed = false;

        $request = $this->getRequest();
        $publicDir = $request->getParam('target', getcwd().'/public/modules');
        $symlink = $request->getParam('symlink');
        $relative = $request->getParam('relative');

        foreach ($this->assets as $name => $originDir) {
            $targetDir = $publicDir.DIRECTORY_SEPARATOR.$name;
            $message = $name;
            try {
                $this->filesystem->remove($targetDir);

                if ($relative) {
                    $expectedMethod = self::METHOD_RELATIVE_SYMLINK;
                    $method = $this->relativeSymlinkWithFallback($originDir, $targetDir);
                } elseif ($symlink) {
                    $expectedMethod = self::METHOD_ABSOLUTE_SYMLINK;
                    $method = $this->absoluteSymlinkWithFallback($originDir, $targetDir);
                } else {
                    $expectedMethod = self::METHOD_COPY;
                    $method = $this->hardCopy($originDir, $targetDir);
                }

                if (self::METHOD_COPY === $method) {
                    $copyUsed = true;
                }

                if ($method === $expectedMethod) {
                    $rows[] = array(sprintf('<fg=green;options=bold>%s</>', '\\' === DIRECTORY_SEPARATOR ? 'OK' : "\xE2\x9C\x94" /* HEAVY CHECK MARK (U+2714) */), $message, $method);
                } else {
                    $rows[] = array(sprintf('<fg=yellow;options=bold>%s</>', '\\' === DIRECTORY_SEPARATOR ? 'WARNING' : '!'), $message, $method);
                }
            } catch (\Exception $e) {
                $exitCode = 1;
                $rows[] = array(sprintf('<fg=red;options=bold>%s</>', '\\' === DIRECTORY_SEPARATOR ? 'ERROR' : "\xE2\x9C\x98" /* HEAVY BALLOT X (U+2718) */), $message, $e->getMessage());
            }
        }

        if ($rows) {
            $io->table(array('', 'Module', 'Method / Error'), $rows);
        }

        if (0 !== $exitCode) {
            $io->error('Some errors occurred while installing assets.');
        } else {
            if ($copyUsed) {
                $io->note('Some assets were installed via copy. If you make changes to these assets you have to run this command again.');
            }
            $io->success($rows ? 'All assets were successfully installed.' : 'No assets were provided by any bundle.');
        }
    }

    private function processModule($module)
    {
        $r = new \ReflectionObject($module);
        $file = $r->getFileName();
        $baseDir = substr($file, 0, strpos($file, 'src'.DIRECTORY_SEPARATOR));
        if (!is_dir($dir = $baseDir.'public')) {
            return;
        }

        $className = get_class($module);
        $moduleName = substr($className, 0, strpos($className, '\\'));
        $this->assets[$moduleName] = $dir;
    }

    /**
     * Try to create absolute symlink.
     *
     * Falling back to hard copy.
     */
    private function absoluteSymlinkWithFallback($originDir, $targetDir)
    {
        try {
            $this->symlink($originDir, $targetDir);
            $method = self::METHOD_ABSOLUTE_SYMLINK;
        } catch (\Exception $e) {
            // fall back to copy
            $method = $this->hardCopy($originDir, $targetDir);
        }

        return $method;
    }

    /**
     * Try to create relative symlink.
     *
     * Falling back to absolute symlink and finally hard copy.
     */
    private function relativeSymlinkWithFallback($originDir, $targetDir)
    {
        try {
            $this->symlink($originDir, $targetDir, true);
            $method = self::METHOD_RELATIVE_SYMLINK;
        } catch (\Exception $e) {
            $method = $this->absoluteSymlinkWithFallback($originDir, $targetDir);
        }

        return $method;
    }

    /**
     * Creates symbolic link.
     *
     * @throws \Exception if link can not be created
     */
    private function symlink($originDir, $targetDir, $relative = false)
    {
        if ($relative) {
            $this->filesystem->mkdir(dirname($targetDir));
            $originDir = $this->filesystem->makePathRelative($originDir, realpath(dirname($targetDir)));
        }
        $this->filesystem->symlink($originDir, $targetDir);
        if (!file_exists($targetDir)) {
            throw new \Exception(
                sprintf('Symbolic link "%s" was created but appears to be broken.', $targetDir),
                0,
                null
            );
        }
    }

    /**
     * Copies origin to target.
     */
    private function hardCopy($originDir, $targetDir)
    {
        $this->filesystem->mkdir($targetDir, 0777);
        // We use a custom iterator to ignore VCS files
        $this->filesystem->mirror($originDir, $targetDir, Finder::create()->ignoreDotFiles(false)->in($originDir));

        return self::METHOD_COPY;
    }
}
