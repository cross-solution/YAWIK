<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Filter\File;

use Core\Entity\FileInterface;
use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Entity extends AbstractFilter
{
    private $alreadyFiltered = [];

    private $fileEntity;

    public function __construct($fileEntityOrOptions)
    {
        if (is_object($fileEntityOrOptions) || is_string($fileEntityOrOptions)) {
            $this->setFileEntity($fileEntityOrOptions);
        } else {
            $this->setOptions($fileEntityOrOptions);
        }
    }

    public function setFileEntity($entity)
    {
        if (is_string($entity)) {
            $entity = new $entity();
        }

        $this->fileEntity = $entity;
    }

    public function getFileEntity()
    {
        if (! $this->fileEntity instanceOf FileInterface) {
            throw new \RuntimeException('No file entity set or it does not implement \Core\Entity\FileInterface.');
        }

        return clone $this->fileEntity;
    }

    public function filter($value)
    {
        if (! is_array($value) || ! isset($value['tmp_name'])) {
            return null;
        }

        if (UPLOAD_ERR_OK != $value['error']) {
            throw new \RuntimeException('File upload failed.');
        }

        if (isset($this->alreadyFiltered[$value['tmp_name']])) {
            return $this->alreadyFiltered[$value['tmp_name']];
        }

        $file = $this->getFileEntity();

        $file->setName($value['name']);
        $file->setType($value['type']);
        $file->setFile($value['tmp_name']);

        $this->alreadyFiltered[$value['tmp_name']] = $file;

        return $file;
    }


}
