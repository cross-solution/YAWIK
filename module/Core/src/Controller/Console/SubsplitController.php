<?php


namespace Core\Controller\Console;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Process\Process;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Zend\Console\Request as ConsoleRequest;

/**
 * Class SubsplitController
 *
 * @package Core\Controller\Console
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.32.0
 */
class SubsplitController extends AbstractConsoleController
{
    const SOURCE = 'git@github.com:cross-solution/YAWIK.git';

    const TARGET = 'git@github.com:yawik';

    private $workdir;

    private $tree;

    private $dryRun = false;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    public static function factory(ContainerInterface $container)
    {
        return new static();
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     * @return SubsplitController
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    public function indexAction()
    {
        $this->workdir = getcwd();
        /* @var ConsoleRequest $request */
        $this->buildTree();
        $workdir = $this->workdir;
        $request = $this->getRequest();
        $this->io = $io = new SymfonyStyle(new ArrayInput([]), $this->output);
        $filter = $request->getParam('module', null);
        $filter = null === $filter ? array() : explode(',', $filter);
        $skipUpdate = $request->getParam('skip-update');
        $heads = $request->getParam('heads', 'develop');
        $tags = $request->getParam('tags', null);
        if ($request->getParam('verbose') || $request->getParam('v')) {
            $this->getOutput()->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }


        $this->dryRun = $request->getParam('dry-run');
        $tree = $this->tree;

        if (!is_dir($dir = $workdir.'/.subsplit')) {
            $this->runCommand('git subsplit init '.static::SOURCE);
        } elseif (!$skipUpdate) {
            $this->runCommand('git subsplit update '.static::SOURCE);
        }

        foreach ($tree as $name => $config) {
            if (count($filter) > 0 && !in_array($name, $filter)) {
                continue;
            }
            $io->newLine();
            $io->writeln("<fg=green>Processing <fg=yellow;options=bold>${name}</> module</>");
            $this->publish($config['path'], $config['repo'], $heads, $tags);
        }
    }

    private function buildTree()
    {
        $dir = getcwd().'/module';
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('You must execute this command from root of yawik development.');
        }
        $finder = Finder::create();
        $finder
            ->in($dir)
            ->depth(0)
        ;
        $tree = [];
        /* @var \Symfony\Component\Finder\SplFileInfo $directory */
        foreach ($finder->directories() as $directory) {
            $moduleName = $directory->getRelativePathname();
            $tree[$moduleName] = [
                'path' => 'module/'.$moduleName,
                'repo' => static::TARGET.'/'.Inflector::tableize($moduleName).'.git'
            ];
        }
        ksort($tree);
        $this->tree = $tree;
    }

    private function publish($path, $repo, $heads = 'master', $tag = null)
    {
        $command = array(
            'git',
            'subsplit',
            'publish',
            '--heads='.$heads,
        );
        if (null !== $tag) {
            $command[] = '--tags='.$tag;
        } else {
            $command[] = '--no-tags';
        }

        $command[] = $path.':'.$repo;

        $command = implode(' ', $command);
        $this->runCommand($command);
    }

    private function runCommand($command)
    {
        if ($this->io->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            $command.=' --debug';
            $this->io->writeln("Executing: <info>$command</info>");
        }
        if ($this->dryRun) {
            return;
        }
        //@codeCoverageIgnoreStart
        $process = new Process($command, $this->workdir);
        $process->setTimeout(null);
        $process->run(array($this, 'handleProcessRun'));
        //@codeCoverageIgnoreEnd
    }

    /**
     * @param string $type
     * @param string $buffer
     * @codeCoverageIgnore
     */
    public function handleProcessRun($type, $buffer)
    {
        $contents = $buffer;
        if (Process::ERR == $type) {
            $contents = $buffer;
        }
        $this->io->write($contents);
    }
}
