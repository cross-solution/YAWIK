<?php

namespace Core\View\Helper\InsertFile;

use Zend\EventManager\Event;

class FileEvent extends Event
{
    const GETFILE = 'file.get';
    const RENDERFILE = 'file.render';
    const INSERTFILE = 'file.announce';
    
    protected $fileNames = array();
    protected $fileObjects = array();
    protected $parameter = array();
    
    public function addFileName($fileName)
    {
        $this->fileNames[] = $fileName;
    }
    
    public function setRenderParameter($parameter = array())
    {
        $this->parameter = $parameter;
        return $this;
    }
    
    public function getRenderParameter()
    {
        return $this->parameter;
    }
    
    public function getLastFileName()
    {
        return $this->fileNames[count($this->fileNames) - 1];
    }
    
    public function setFileObject($fileName, $file)
    {
        $this->fileObjects[$fileName] = $file;
        return $this;
    }
    
    public function getLastFileObject()
    {
        $lastName = $this->getLastFileName();
        if (is_string($lastName)) {
            if (array_key_exists($lastName, $this->fileObjects)) {
                return $this->fileObjects[$lastName];
            }
            return null;
        }
        // assume it is already an Object
        return $lastName;
    }
    
    public function getAllFiles()
    {
        $erg = array();
        foreach ($this->fileNames as $obj) {
            if (is_string($obj)) {
                if (array_key_exists($obj, $this->fileObjects)) {
                    $erg[] = $this->fileObjects[$obj];
                }
            } else {
                $erg[] = $obj;
            }
        }
        return $erg;
    }
}
