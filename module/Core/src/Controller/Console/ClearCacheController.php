<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\Controller\Console;

use Core\Service\ClearCacheService;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

/**
 * Clean Cache
 *
 * @package Core\Controller\Console
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.32
 */
class ClearCacheController extends AbstractConsoleController
{
    /**
     * @var ClearCacheService
     */
    private $cache;

    /**
     * CacheWarmupController constructor.
     * @param ClearCacheService $cache
     */
    public function __construct(ClearCacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ContainerInterface $container
     * @return ClearCacheController
     */
    public static function factory(ContainerInterface $container)
    {
        $cache = $container->get(ClearCacheService::class);

        return new static($cache);
    }

    /**
     * Clear cache
     * @return void|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $console = $this->getConsole();
        $console->writeLine('Cleaning up all cache files.');
        $this->cache->clearCache();
    }
}
