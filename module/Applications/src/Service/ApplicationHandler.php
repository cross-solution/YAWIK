<?php

declare(strict_types=1);

namespace Applications\Service;


use Applications\Entity\Application;
use Applications\Entity\ApplicationInterface;
use Core\Entity\FileMetadata;
use Core\Entity\ImageMetadata;
use Core\Service\FileManager;
use Core\Service\UploadedFileInfo;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Container\ContainerInterface;

class ApplicationHandler
{
    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    public function __construct(
        DocumentManager $dm,
        FileManager $fileManager
    )
    {
        $this->dm = $dm;
        $this->fileManager = $fileManager;
    }

    public static function factory(ContainerInterface $container)
    {
        $dm = $container->get(DocumentManager::class);
        $fileManager = $container->get(FileManager::class);
        return new self($dm, $fileManager);
    }

    public function find(string $id)
    {
        return new Application();
    }

    public function handleUpload(
        ApplicationInterface $application,
        UploadedFileInfo $info
    ): ApplicationInterface
    {
        $dm = $this->dm;
        $fileManager = $this->fileManager;
        $metadata = new FileMetadata();

        $metadata
            ->setUser($user)
            ->setContentType($info->getContentType())
        ;
    }

}