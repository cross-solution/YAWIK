<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\Service;

use Core\Application;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\Stdlib\Glob;

/**
 * Class CacheWarmupService
 *
 * @package Core\Service
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.32
 */
class ClearCacheService
{
    /**
     * @var ListenerOptions
     */
    private $options;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * Clear cache constructor.
     * @param ListenerOptions $options
     * @param Filesystem|null $filesystem
     */
    public function __construct(ListenerOptions $options, Filesystem $filesystem = null)
    {
        if (is_null($filesystem)) {
            $filesystem = new Filesystem();
        }
        $this->options = $options;
        $this->filesystem = $filesystem;
        if (php_sapi_name() === 'cli') {
            $this->io = new SymfonyStyle(new StringInput(''), new ConsoleOutput());
        }
    }

    /**
     * Creates new ClearCacheService object
     * @param ContainerInterface $container
     * @return ClearCacheService
     */
    public static function factory(ContainerInterface $container)
    {
        /* @var \Zend\ModuleManager\ModuleManager $manager */
        $config = $container->get('ApplicationConfig');
        $options = new ListenerOptions($config['module_listener_options']);
        return new static($options);
    }

    /**
     * Clear all cache files in cache directory.
     * Only cleans cache file in path/to/yawik/var/cache/*.php.
     * Files in sub cache directory will not be removed
     *
     * @throws \Exception when cache directory is null
     * @throws \Exception when cache directory is not exists or not writable
     */
    public function clearCache()
    {
        // do not clear cache when cache directory not exists
        $cacheDir = $this->options->getCacheDir();
        if (is_null($cacheDir)) {
            throw new \Exception(sprintf(
                'Cache directory is not configured properly.'
            ));
        }
        if (!is_dir($cacheDir) || !is_writable($cacheDir)) {
            throw new \Exception(
                sprintf(
                    'Can not clear cache in "%s". Please be sure that directory exists and writable.',
                    $cacheDir
                )
            );
        }
        $finder = Finder::create()
            ->in($cacheDir)
            ->ignoreDotFiles(false)
            ->name('*.php')
            ->name('.checksum')
            ->depth(0)
        ;
        try {
            $this->filesystem->remove($finder);
            return true;
        } catch (\Exception $e) {
            // just log the error
            $this->log('<error>'.$e->getMessage().'</error>');
            return false;
        }
    }

    /**
     * This function will check cache by creating md5 sum
     * from all file modification time in config/autoload/*.php.
     * If checksum is invalid it will automatically call clear cache.
     */
    public function checkCache()
    {
        $options = $this->options;

        $configDir = Application::getConfigDir();

        $cacheDir = $options->getCacheDir();

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $mtimes = [];
        $mtimes[] = filemtime($configDir.'/config.php');

        foreach ($options->getConfigGlobPaths() as $path) {
            foreach (Glob::glob($path, Glob::GLOB_BRACE) as $file) {
                $mtimes[] = filemtime($file);
            }
        }

        $checksum = md5(serialize($mtimes));

        $checksumFile = $options->getCacheDir().'/.checksum';
        if (!file_exists($checksumFile)) {
            touch($checksumFile);
        }
        if (is_readable($checksumFile)) {
            $cacheSum = file_get_contents($checksumFile);
            if ($cacheSum != $checksum) {
                $this->clearCache();
                file_put_contents($checksumFile, $checksum, LOCK_EX);
            }
        } else {
            $this->log("Can\'t process cache .checksum file is not readable.");
        }
    }

    private function log($message)
    {
        $io = $this->io;
        if ($io instanceof SymfonyStyle) {
            $io->writeln($message);
        }
    }
}
