<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileUploadStrategy.php */ 
namespace Core\Entity\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Core\Entity\FileInterface;
use Core\Entity\FileEntity;
use Auth\Entity\UserInterface;
use Auth\Entity\User;
use Auth\Entity\Info;

class FileUploadStrategy implements StrategyInterface
{
    protected $fileEntity;
    protected $user;
    
    
    public function __construct(FileInterface $file)
    {
        $this->setFileEntity($file);
    }
    
    public function setFileEntity(FileInterface $file)
    {
        $this->fileEntity = $file;
        return $this;
    }
    
    public function getFileEntity()
    {
        if (!$this->fileEntity) {
            $file = new FileEntity();
            $this->setFileEntity($file);
        }
        return clone $this->fileEntity;
    }
    
    public function extract ($value)
    {
        if (!$value instanceOf FileInterface) {
            return null;
        }
        
        return $value->getId();
    }

    public function hydrate ($value)
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

