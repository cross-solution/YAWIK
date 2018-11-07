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

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Entity extends AbstractFilter
{
    private $alreadyFiltered = [];

    private $fileEntity;

    private $repository;

    private $user;

    public function __construct($fileEntityOrOptions)
    {
        if (is_object($fileEntityOrOptions) || is_string($fileEntityOrOptions)) {
            $this->setFileEntity($fileEntityOrOptions);
        } elseif (is_array($fileEntityOrOptions)) {
            $this->setOptions($fileEntityOrOptions);
        }
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }



    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repositories
     *
     * @return self
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
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
        if (! $this->fileEntity instanceof FileInterface) {
            throw new \RuntimeException('No file entity set or it does not implement \Core\Entity\FileInterface.');
        }

        return clone $this->fileEntity;
    }

    public function filter($value)
    {
        if (! is_array($value) || ! isset($value['tmp_name']) || ((isset($value['error']) && UPLOAD_ERR_NO_FILE == $value['error']))) {
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
        if ($user = $this->getUser()) {
            $file->setUser($user);
        }

        if ($repository = $this->getRepository()) {
            $repository->store($file);
        }

        $value['entity'] = $file;

        $this->alreadyFiltered[$value['tmp_name']] = $value;

        return $value;
    }
}
