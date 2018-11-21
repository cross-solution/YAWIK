<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ReleaseTools\Console;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Core\Module as CoreModule;

class ReleaseController extends AbstractConsoleController
{
    /**
     * @var SymfonyStyle
     */
    private $output;

    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $dryRun;

    public function __construct(array $config)
    {
        $output     = new ConsoleOutput();
        $input      = new ArgvInput();
        $style      = new SymfonyStyle($input, $output);

        $this->output = $style;
        $this->config = $config;
    }

    public static function getConsoleUsage()
    {
        return [
            'release <tags>' => 'Releasing Yawik into given tags version',
            'The release command will automatically perform git tag in cross-solution/YAWIK repo and yawik/* repository',
            "",
            ["-m", 'Define message to be included in git release -m command'],
            ["--dry-run", 'Nothing will change, only display git command that will executed during release process.'],
            "",
            "",
        ];
    }

    public static function factory(ContainerInterface $container)
    {
        $config = $container->get('Config');
        $config = $config['release'];
        return new static($config);
    }

    public function indexAction()
    {
        $output         = $this->output;
        $request        = $this->getRequest();
        $tag            = $request->getParam('tag');
        $defMsg         = "Release {$tag} version";
        $message        = $request->getParam('message', $defMsg);
        $this->dryRun   = $request->getParam('dry-run', false);

        $currentBranch = `git branch | grep \* | cut -d ' ' -f2`;

        $output->section("Releasing Yawik into {$tag} version.");

        if (!$this->validateTag($tag)) {
            return false;
        }
        try {
            $this->createMainDevelopmentTag($tag, $message);
            $this->createSubsplitTag($tag, $message);
            $output->newLine(2);
            $output->writeln("Releasing process completed.\n<info>Welcome to <fg=yellow;options=bold>{$tag}</> version!</>");
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }

        $output->newLine();
        $this->execute("git checkout {$currentBranch}");
    }

    public function createMainDevelopmentTag($tag, $message)
    {
        $config         = $this->config;
        $mainRemoteName = $config['main_remote_name'];
        $output         = $this->output;

        $output->newLine(1);
        $output->writeln("<info>Releasing <comment>{$tag}</comment> to ~> <comment>cross-solution/YAWIK</comment> repo</info>");
        $this->execute('git checkout master');
        $this->execute("git tag -s {$tag} -m \"{$message}\"");
        $this->execute("git push {$mainRemoteName} {$tag}");
    }

    private function createSubsplitTag($tag, $message)
    {
        $config         = $this->config;
        $output         = $this->output;
        $cwd            = $config['subsplit_clone_dir'];
        $subsplit = [
            'applications',
            'auth',
            'behat',
            'core',
            'cv',
            'geo',
            'install',
            'jobs',
            'organizations',
            'pdf',
            'settings',

        ];
        foreach ($subsplit as $repo) {
            try {
                $repoDir    = $cwd.DIRECTORY_SEPARATOR.$repo;
                $remoteUrl  = "git@github.com:yawik/{$repo}.git";
                $output->newLine(2);
                $output->writeln("<info>Releasing <comment>{$tag}</comment> to ~> {$remoteUrl}</info>");
                $this->execute("git clone {$remoteUrl} .", $repoDir);
                $this->execute("git checkout master");
                $this->execute("git tag -s ${tag} -m \"{$message}\"");
                $this->execute("git push origin {$tag}");
            } catch (\Exception $exception) {
                $output->writeln('<error>'.$exception->getMessage().'</error>');
            }
        }
    }

    private function validateTag($tag)
    {
        $output = $this->output;
        $moduleVersion  = CoreModule::VERSION;

        if (false===strpos($tag, $moduleVersion)) {
            $message = <<<EOC
<error>Can't releasing <bg=red;options=bold>$tag</> version.
Please be sure that <bg=red;options=bold>Core\Module::VERSION</> constant is having
same version with your release tags.
Expected tag release value is: <bg=red;options=bold>$tag</>
<bg=red;options=bold>Core\Module::VERSION</> value is: <bg=red;options=bold>$moduleVersion</></error>
EOC;
            $output->writeln($message);
            return false;
        }
        return true;
    }

    public function execute($command, $cwd = null)
    {
        $output     = $this->output;

        if (!is_dir($cwd)) {
            mkdir($cwd);
        }

        $output->writeln('<bg=white;options=bold> CMD </> '.$command);
        if (!$this->dryRun) {
            $process    = new Process($command, $cwd);
            $process->setTimeout(3600);
            return $process->run([$this,'onExecuteCommand']);
        }
        return 'command not executed in dry-run mode.';
    }

    public function onExecuteCommand($type, $buffer)
    {
        $flag = '<bg=green;options=bold> OUT </>';
        if (Process::ERR === $type) {
            $flag = '<bg=red;options=bold> ERR </>';
        }

        $exp = explode(PHP_EOL, $buffer);
        foreach ($exp as $item) {
            if ("" == trim($item)) {
                continue;
            }
            $this->output->writeln($flag.' '.$item);
        }
    }
}
