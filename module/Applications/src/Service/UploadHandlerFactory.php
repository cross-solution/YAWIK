<?php

declare(strict_types=1);

namespace Applications\Service;


use Applications\Entity\Application;
use Core\Service\FileManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Container\ContainerInterface;

class UploadHandlerFactory
{
    public function __invoke(ContainerInterface $container): UploadHandler
    {
        $dm = $container->get(DocumentManager::class);
        $fileManager = $container->get(FileManager::class);
        $appRepo = $dm->getRepository(Application::class);

        return new UploadHandler($dm, $fileManager, $appRepo);
    }
}