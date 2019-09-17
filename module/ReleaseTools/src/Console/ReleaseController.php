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
        $targetBranch   = $request->getParam('branch', 'master');

        $output->section("Releasing Yawik into {$tag} version.");

        if (!$this->validateTag($tag)) {
            return false;
        }
        try {
            $this->createMainDevelopmentTag($targetBranch, $tag, $message);
            $this->createSubsplitTag($targetBranch, $tag, $message);
            $output->newLine(2);
            $output->writeln("Releasing process completed.\n<info>Welcome to <fg=yellow;options=bold>{$tag}</> version!</>");
            //$this->bumpModuleVersion($targetBranch, $tag);
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }

        $output->newLine();
        $this->execute("git checkout {$currentBranch}", getcwd());
    }

    public function createMainDevelopmentTag($targetBranch, $tag, $message)
    {
        $config         = $this->config;
        $mainRemoteName = $config['main_remote_name'];
        $output         = $this->output;
        $cwd            = getcwd();

        $output->newLine(1);
        $output->writeln("<info>Releasing <comment>{$tag}</comment> to ~> <comment>cross-solution/YAWIK</comment> repo</info>");
        $this->execute("git checkout {$targetBranch}", $cwd);
        $this->execute("git pull {$mainRemoteName} {$targetBranch}", $cwd);
        $this->execute("git tag -s {$tag} -m \"{$message}\"", $cwd);
        $this->execute("git push {$mainRemoteName} {$tag}", $cwd);
    }

    private function createSubsplitTag($targetBranch, $tag, $message)
    {
        $config         = $this->config;
        $output         = $this->output;
        $cwd            = $config['subsplit_clone_dir'];

        $output->newLine(2);
        $output->writeln("<info>Prepare package releases...</info>");

        if (!is_dir($cwd) && 0 !== $this->execute("mkdir -p {$cwd}", getcwd())) {
            $output->writeln('<error>Temporary directory could not be created.</error>');
            return;
        }

        $subsplit = [
            'applications',
            'auth',
            'behat',
            'core',
            'cv',
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
                $output->writeln("<info>Working Directory: </info> <comment>{$repoDir}</comment>");
                if (!is_dir($repoDir) && 0 !== $this->execute("mkdir {$repoDir}", getcwd())) {
                    throw new \Exception('Working directory could not be created.');
                }

                $this->execute("git clone {$remoteUrl} .", $repoDir);
                $this->execute("git pull origin {$targetBranch}", $repoDir);
                $this->execute("git tag -d ${tag}", $repoDir);
                $this->execute("git push origin :{$tag}", $repoDir);
                $this->execute("git checkout {$targetBranch}", $repoDir);
                $this->execute("git tag -s ${tag} -m \"{$message}\"", $repoDir);
                $this->execute("git push origin {$tag}", $repoDir);
            } catch (\Exception $exception) {
                $output->writeln('<error>'.$exception->getMessage().'</error>');
            }
        }

        $output->newLine(2);
        $output->writeln("<info>Cleanup</info>");
        if (is_dir($cwd)) {
            $this->execute("rm -rf {$cwd}", getcwd());
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

    private function bumpModuleVersion($targetBranch, $tag)
    {
        $file       = realpath(__DIR__.'/../../../Core/src/Module.php');
        $contents   = file_get_contents($file);
        $pattern    = "/\s+const\s+VERSION\s+=\s+\'(.+)\'/i";
        $matches    = array();
        $output     = $this->output;

        // creating dev tags version
        $tag        = str_replace('v', '', $tag);
        $exp        = explode('.', $tag);
        $major      = $exp[0];
        $minor      = $exp[1]+1;
        $devVersion = sprintf('%s.%s-dev', $major, $minor);

        $output->section('Bump Core\Module::VERSION');
        preg_match($pattern, $contents, $matches);
        if (isset($matches[1])) {
            if (!$this->dryRun) {
                $contents = str_replace($matches[1], $devVersion, $contents);
                file_put_contents($file, $contents, LOCK_EX);
            }
            $output->writeln(
                "Version changed to: <comment>{$devVersion}</comment>\n"
                ."Please <info>git commit</info> and git push this change to <comment>cross-solution/YAWIK</comment>"
            );
        } else {
            $output->note("Can not change version automatically.\nYou have to do this manually!");
        }
    }

    public function execute($command, $cwd = null)
    {
        if (is_null($cwd)) {
            throw new \Exception('You have to define current directory to working with');
        }

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
