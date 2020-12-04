<?php

declare(strict_types=1);

namespace Auth\Service;


use Auth\AuthenticationService;
use Auth\Entity\UserImage;
use Auth\Entity\UserInterface;
use Core\Entity\ImageMetadata;
use Core\Service\FileManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Container\ContainerInterface;

class UploadHandler
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var AuthenticationService
     */
    private AuthenticationService $auth;

    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;

    public function __construct(
        DocumentManager $dm,
        FileManager $fileManager
    )
    {
        $this->fileManager = $fileManager;
        $this->dm = $dm;
    }

    public static function factory(ContainerInterface $container): self
    {
        $dm = $container->get(DocumentManager::class);
        $fileManager = $container->get(FileManager::class);
        return new self($dm, $fileManager);
    }

    public function handleUpload(UserInterface $user, array $uploaded): UserInterface
    {
        /* @var UserInterface $user */
        $dm = $this->dm;
        $fileManager = $this->fileManager;
        $metadata = new ImageMetadata();

        if(!is_null($image = $user->getInfo()->getImage())){
            $user->getInfo()->setImage(null);
            $this->dm->persist($user);
            $this->dm->flush();
        }

        $metadata
            ->setUser($user)
            ->setContentType($uploaded['type'])
            ->setKey('original')
        ;
        $this->dm->persist($user);
        $file = $fileManager->uploadFromFile(
            UserImage::class,
            $metadata,
            $uploaded['tmp_name'],
            $uploaded['name']
        );
        $user->getInfo()->setImage($file);
        $dm->flush();

        return $user;
    }
}