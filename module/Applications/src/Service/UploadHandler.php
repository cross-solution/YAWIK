<?php

declare(strict_types=1);

namespace Applications\Service;


use Applications\Entity\Application;
use Applications\Entity\ApplicationInterface;
use Applications\Entity\Attachment;
use Core\Entity\FileMetadata;
use Core\Service\FileManager;
use Core\Service\UploadedFileInfo;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;

class UploadHandler
{
    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var ObjectRepository
     */
    private ObjectRepository $appRepository;

    public function __construct(
        DocumentManager $dm,
        FileManager $fileManager,
        ObjectRepository $appRepository
    )
    {
        $this->dm = $dm;
        $this->fileManager = $fileManager;
        $this->appRepository = $appRepository;
    }

    /**
     * @param string $id
     * @return object|null|ApplicationInterface
     */
    public function findApplication(string $id)
    {
        return $this->appRepository->find($id);
    }

    /**
     * @param string $appId
     * @param array $info
     * @return object|Attachment
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function handleAttachmentUpload(
        string $appId,
        array $info
    )
    {
        $application = $this->findApplication($appId);
        $attachment = $this->doUploadFile($application, $info);
        $dm = $this->dm;

        $application->getAttachments()->add($attachment);
        $dm->persist($application);
        $dm->flush();

        return $attachment;
    }

    /**
     * @param string $appID
     * @param array $info
     * @return object|ApplicationInterface
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function handleImageUpload(
        string $appID,
        array $info
    )
    {
        $dm = $this->dm;
        $application = $this->findApplication($appID);
        $attachment = $this->doUploadFile($application, $info);

        // remove existing image
        if(!is_null($application->getContact()->getImage())){
            $image = $application->getContact()->getImage();
            $application->getContact()->setImage(null);
            $dm->remove($image);
            $dm->persist($application);
            $dm->flush();
        }

        $application->getContact()->setImage($attachment);
        $dm->persist($application);
        $dm->flush();

        return $application;
    }

    private function doUploadFile(
        ApplicationInterface $application,
        array $info
    )
    {
        $fileManager = $this->fileManager;
        $user = $application->getUser();

        $metadata = new FileMetadata();
        $metadata->setUser($user);
        $metadata->setContentType($info['type']);
        $metadata->setName($info['name']);

        $this->dm->persist($user);
        return $fileManager->uploadFromFile(
            Attachment::class,
            $metadata,
            $info['tmp_name'],
            $info['name']
        );
    }
}