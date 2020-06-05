<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace Organizations\Factory\Controller;

use Interop\Container\ContainerInterface;
use Organizations\Controller\ProfileController;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Create new ProfileController object
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Organizations\Factory\Controller
 * @since 0.30
 */
class ProfileControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repo = $container->get('repositories')
            ->get('Organizations/Organization')
        ;
        $jobRepository = $container->get('repositories')
            ->get('Jobs/Job')
        ;
        $translator = $container->get('translator');
        $imageFileCacheManager = $container->get('Organizations\ImageFileCache\Manager');
        $options = $container->get('Jobs/JobboardSearchOptions');
        return new ProfileController($repo, $jobRepository, $translator, $imageFileCacheManager, ['count' => $options->getPerPage()]);
    }
}
