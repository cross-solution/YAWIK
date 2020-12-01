<?php

declare(strict_types=1);

namespace Organizations\Service;


use Core\Entity\ImageInterface;
use Core\Service\FileManager;
use Core\Service\ImageSetHandler;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationImageMetadata;
use Organizations\Entity\OrganizationInterface;
use Organizations\Options\OrganizationLogoOptions;
use Psr\Container\ContainerInterface;

class ManageHandler
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;

    private ObjectRepository $orgRepository;
    /**
     * @var OrganizationLogoOptions
     */
    private OrganizationLogoOptions $logoOptions;
    /**
     * @var ImageSetHandler
     */
    private ImageSetHandler $imageSet;

    public function __construct(
        DocumentManager $dm,
        FileManager $fileManager,
        OrganizationLogoOptions $logoOptions,
        ImageSetHandler $imageSet
    )
    {
        $this->dm = $dm;
        $this->orgRepository = $dm->getRepository(Organization::class);
        $this->fileManager = $fileManager;
        $this->logoOptions = $logoOptions;
        $this->imageSet = $imageSet;
    }

    public static function factory(ContainerInterface $container): self
    {
        $dm = $container->get(DocumentManager::class);
        $fileManager = $container->get(FileManager::class);
        $logoOptions = $container->get(OrganizationLogoOptions::class);
        $imageSet = $container->get(ImageSetHandler::class);

        return new self($dm, $fileManager, $logoOptions, $imageSet);
    }

    public function handleLogoUpload(string $oganizationID, array $data): OrganizationInterface
    {
        /* @var OrganizationInterface $organization */
        $organization = $this->orgRepository->find($oganizationID);
        $dm = $this->dm;
        $fileManager = $this->fileManager;
        $options = $this->logoOptions;
        $imageSet = $this->imageSet;
        $tmpDir = sys_get_temp_dir().'/yawik/images';

        $imageSetID = $organization->getImages()->getId();
        $images = $imageSet->createImages($options->getImages(), $data);

        if(!is_dir($tmpDir)){
            mkdir($tmpDir, 0777, true);
        }

        foreach($images as $key => $image){
            $name = $key.'-'. $data['name'];
            $format = str_replace('image/', '', $data['type']);
            $content = $image->get($format);
            $tmpFile = $tmpDir.DIRECTORY_SEPARATOR.md5($image->get($format));
            file_put_contents($tmpFile, $content);

            $metadata = new OrganizationImageMetadata();
            $metadata->setBelongsTo($imageSetID);
            $metadata->setOrganization($organization);
            $metadata->setKey($key);
            $metadata->setContentType($data['type']);

            /* @var ImageInterface $file */
            $file = $fileManager->uploadFromFile($options->getEntityClass(),$metadata, $tmpFile, $name);
            $organization->getImages()->add($file);
        }

        $dm->persist($organization);
        $dm->flush();
        $dm->refresh($organization);
        return $organization;
    }

}