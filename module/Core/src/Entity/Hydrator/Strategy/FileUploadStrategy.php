<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileUploadStrategy.php */
namespace Core\Entity\Hydrator\Strategy;

use Auth\Entity\User;
use Core\Entity\File;
use Core\Entity\FileMetadataInterface;
use Core\Service\FileManager;
use Laminas\Hydrator\Strategy\StrategyInterface;
use Core\Entity\FileInterface;

class FileUploadStrategy implements StrategyInterface
{
    protected ?FileMetadataInterface $metadata = null;
    private User $user;
    private string $metadataClass;
    private FileManager $fileManager;
    private string $entityClass;

    /**
     * FileUploadStrategy constructor.
     *
     * @param FileManager $fileManager
     * @param User $user
     * @param string $metadataClass
     * @param string $entityClass
     */
    public function __construct(
        FileManager $fileManager,
        User $user,
        string $metadataClass,
        string $entityClass
    ){

        $this->user = $user;
        $this->metadataClass = $metadataClass;
        $this->fileManager = $fileManager;
        $this->entityClass = $entityClass;
    }

    /**
     * @param FileMetadataInterface $metadata
     *
     * @return $this
     */
    public function setMetadata(FileMetadataInterface $metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @return FileMetadataInterface
     */
    public function getMetadata()
    {
        if (is_null($this->metadata)) {
            $metadata = new $this->metadataClass();
            $this->setMetadata($metadata);
        }
        return clone $this->metadata;
    }

    /**
     * @param mixed $value
     *
     * @param object|null $object
     * @return mixed|null
     */
    public function extract($value, ?object $object = null)
    {
        if (!$value instanceof FileInterface) {
            return null;
        }
        
        return $value->getId();
    }

    /**
     * @param mixed $value
     *
     * @param array|null $data
     * @return object|File|null
     */
    public function hydrate($value, ?array $data)
    {
        if (!UPLOAD_ERR_OK == $value['error']) {
            return null;
        }
        $fileManager = $this->fileManager;
        $metadata = $this->getMetadata();
        
        $metadata
            ->setContentType($value['type'])
            ->setUser($this->user)
        ;

        return $fileManager->uploadFromFile(
            $this->entityClass,
            $metadata,
            $value['tmp_name'],
            $value['name']
        );
    }
}
