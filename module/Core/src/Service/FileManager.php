<?php

declare(strict_types=1);

namespace Core\Service;


use Auth\AuthenticationService;
use Auth\Entity\AnonymousUser;
use Core\Entity\FileInterface;
use Core\Entity\FileMetadataInterface;
use Core\Entity\ImageInterface;
use Core\Entity\PermissionsInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Imagine\Image\ImageInterface as ImagineImage;
use Doctrine\ODM\MongoDB\Repository\GridFSRepository;
use Doctrine\ODM\MongoDB\Repository\UploadOptions;
use Doctrine\Persistence\ObjectRepository;
use Psr\Container\ContainerInterface;

class FileManager
{
    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;
    /**
     * @var AuthenticationService
     */
    private AuthenticationService $auth;

    public function __construct(
        DocumentManager $dm,
        AuthenticationService $auth
    )
    {
        $this->dm = $dm;
        $this->auth = $auth;
    }

    public static function factory(ContainerInterface $container): self
    {
        $dm = $container->get(DocumentManager::class);
        $auth = $container->get('AuthenticationService');
        return new FileManager($dm, $auth);
    }

    public function findByID(string $entityClass, string $id): ?object
    {
        $repo = $this->getRepository($entityClass);
        return $repo->find($id);
    }

    /**
     * @param FileInterface $file
     * @return resource
     */
    public function getStream(FileInterface $file)
    {
        $repo = $this->getRepository(get_class($file));
        return $repo->openDownloadStream($file->getId());
    }

    public function getContents(FileInterface $file): string
    {
        $repo = $this->getRepository(get_class($file));
        $stream = $repo->openDownloadStream($file->getId());
        return stream_get_contents($stream);
    }

    public function uploadFromFile(string $entityClass, FileMetadataInterface $metadata, string $source, ?string $fileName = null): object
    {
        $repo = $this->getRepository($entityClass);
        $user = $this->auth->getUser();

        if($user instanceof AnonymousUser){
            $metadata->getPermissions()->grant($user, PermissionsInterface::PERMISSION_ALL);
        }else{
            $this->dm->persist($user);
            $metadata->setUser($user);
        }

        $options = new UploadOptions();
        $options->metadata = $metadata;
        $file = $repo->uploadFromFile($source, $fileName, $options);
        $this->dm->persist($file);

        return $file;
    }

    /**
     * @param string $entityClass
     * @param FileMetadataInterface $metadata
     * @param string $fileName
     * @param resource $stream
     * @return object|FileInterface|ImageInterface
     */
    public function uploadFromStream(string $entityClass, FileMetadataInterface $metadata, string $fileName, $stream): object
    {
        $repo = $this->getRepository($entityClass);
        $options = new UploadOptions();
        $options->metadata = $metadata;

        $repo->uploadFromStream($fileName, $stream, $options);
    }

    public function remove(FileInterface $file, $andFlush = false)
    {
        $this->dm->remove($file);
        if($andFlush){
            $this->dm->flush();
        }
    }

    /**
     * @param string $entityClass
     * @return ObjectRepository|GridFSRepository
     */
    private function getRepository(string $entityClass)
    {
        return $this->dm->getRepository($entityClass);
    }
}