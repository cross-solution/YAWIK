<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Util\Inflector;

/**
 * Class SubsplitCommand.
 *
 * @codeCoverageIgnore
 */
class SubsplitCommand extends Command
{
    // @todo Change this into git@github.com:cross-solution/yawik.git repo when make a pull request
    const SOURCE = 'git@github.com:kilip/yawik.git';

    const TARGET = 'git@github.com:yawik';

    /**
     * @var OutputInterface
     */
    private $output;

    private $workdir;

    private $tree;

    protected function configure()
    {
        $this->setName('dev:subsplit')
            ->addArgument('package', InputArgument::OPTIONAL, 'Module to subsplit', null)
            ->addOption('heads', null, InputOption::VALUE_OPTIONAL, 'Repository Heads', 'develop')
            ->addOption('skip-update', 's', InputOption::VALUE_NONE, 'Skip subsplit update')
            ->addOption('tags', null, InputOption::VALUE_OPTIONAL, 'Repository tags to include', null)
        ;
        $this->workdir = getcwd();
        $this->buildTree();
    }

    protected function buildTree()
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $workdir = $this->workdir;
        $filter = $input->getArgument('package');
        $filter = null === $filter ? array() : explode(',', $filter);

        $tree = $this->tree;


        if (!is_dir($dir = $workdir.'/.subsplit')) {
            $this->runCommand('git subsplit --debug init '.static::SOURCE);
        } elseif (!$input->getOption('skip-update')) {
            $this->runCommand('git subsplit --debug update '.static::SOURCE);
        }

        $heads = $input->getOption('heads');
        $tags = $input->getOption('tags');
        foreach ($tree as $name => $config) {
            if (count($filter) > 0 && !in_array($name, $filter)) {
                continue;
            }
            $this->output->writeln("processing <comment>$name</comment>");
            $this->publish($config['path'], $config['repo'], $heads, $tags);
        }
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
        //$this->output->writeln("<comment>$command</comment>");
        $this->runCommand($command);
    }

    private function runCommand($command)
    {
        $process = new Process($command, $this->workdir);
        $process->setTimeout(null);
        $process->run(array($this, 'handleProcessRun'));
    }

    public function handleProcessRun($type, $buffer)
    {
        $contents = '<info>output:</info> '.$buffer;
        if (Process::ERR == $type) {
            $contents = '<error>error:</error> '.$buffer;
        }
        $this->output->write($contents);
    }
}
