<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Core\Factory\Service;


use Core\Options\ModuleOptions;
use HTMLPurifier;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class HtmlPurifierFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return HTMLPurifier
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): HTMLPurifier
    {
        /* @var \Core\Options\ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        $cacheDir = $options->getCacheDir();

        if(!is_dir($cacheDir)){
            mkdir($cacheDir, 0775, true);
        }

        $config = [
            'Cache.SerializerPath' => $cacheDir
        ];

        return new HTMLPurifier($config);
    }
}