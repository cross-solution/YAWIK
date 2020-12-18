<?php

declare(strict_types=1);

namespace Core\Service;


use Auth\AuthenticationService;
use Auth\Entity\AnonymousUser;
use Auth\Entity\UserImage;
use Core\Entity\FileInterface;
use Core\Entity\FileMetadataInterface;
use Core\Entity\ImageInterface;
use Core\Entity\PermissionsInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Imagine\Image\ImageInterface as ImagineImage;
use Doctrine\ODM\MongoDB\Repository\GridFSRepository;
use Doctrine\ODM\MongoDB\Repository\UploadOptions;
use Doctrine\Persistence\ObjectRepository;
use Imagine\Image\ImagineInterface;
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

    /**
     * @param string $entityClass
     * @param string $id
     * @return object|null|FileInterface
     */
    public function findByID(string $entityClass, string $id)
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

        if(UserImage::class !== $entityClass && is_null($metadata->getUser())){
            $user = $this->auth->getUser();
            if($user instanceof AnonymousUser){
                $metadata->getPermissions()->grant($user, PermissionsInterface::PERMISSION_ALL);
            }else{
                $metadata->setUser($user);
                $this->dm->persist($user);
                $this->dm->flush();
            }
        }

        $options = new UploadOptions();
        $options->metadata = $metadata;

        return $repo->uploadFromFile($source, $fileName, $options);
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

        return $repo->uploadFromStream($fileName, $stream, $options);
    }

    public function remove(FileInterface $file, $andFlush = false)
    {
        $dm = $this->dm;
        $events = $dm->getEventManager();

        $dm->remove($file);
        $events->hasListeners('postRemoveEntity') &&
            $events->dispatchEvent('postRemoveEntity', new LifecycleEventArgs($file, $dm));

        if($andFlush){
            $dm->flush();
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