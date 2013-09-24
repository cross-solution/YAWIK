<?php

namespace Core\Entity;



interface FileEntityInterface
{
    public function getName();
    public function setName($name);
    
    public function getSize();
    public function setSize($bytes);
    
    public function getType();
    public function setType($mimeType);
    
    public function getDateUploaded();
    public function setDateUploaded(\DateTime $date);
    
    public function getContent();
    public function setContent($content);
    public function setContentCallback($callable);
    
     
}
