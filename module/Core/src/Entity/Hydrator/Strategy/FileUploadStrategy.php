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

use Laminas\Hydrator\Strategy\StrategyInterface;
use Core\Entity\FileInterface;
use Core\Entity\FileEntity;

class FileUploadStrategy implements StrategyInterface
{
    /**
     * @var FileEntity
     */
    protected $fileEntity;

    /**
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {
        $this->setFileEntity($file);
    }

    /**
     * @param FileInterface $file
     *
     * @return $this
     */
    public function setFileEntity(FileInterface $file)
    {
        $this->fileEntity = $file;
        return $this;
    }

    /**
     * @return FileEntity
     */
    public function getFileEntity()
    {
        if (!$this->fileEntity) {
            $file = new FileEntity();
            $this->setFileEntity($file);
        }
        return clone $this->fileEntity;
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
     * @return FileEntity|null
     */
    public function hydrate($value, ?array $data)
    {
        if (!UPLOAD_ERR_OK == $value['error']) {
            return null;
        }
        
        $file = $this->getFileEntity();
        
        $file->setName($value['name'])
             ->setType($value['type'])
             ->setFile($value['tmp_name']);
        
        return $file;
    }
}
