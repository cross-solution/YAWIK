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
     * CacheWarmupService constructor.
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
    }

    /**
     * Creates new service
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
     * Clean all cache in directory
     */
    public function clearCache()
    {
        $cacheDir = $this->options->getCacheDir();
        $finder = Finder::create()
            ->in($cacheDir)
            ->ignoreDotFiles(false)
            ->name('*.php')
            ->name('.checksum')
            ->files()
        ;
        $fs = $this->filesystem;
        $fs->remove($finder);
    }

    public function checkCache()
    {
        $options = $this->options;

        $configDir = Application::getConfigDir();
        $env = Application::$env;
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
        $cacheSum = file_get_contents($checksumFile);
        if ($cacheSum != $checksum) {
            $this->clearCache();
            file_put_contents($checksumFile, $checksum, LOCK_EX);
        }
    }
}
