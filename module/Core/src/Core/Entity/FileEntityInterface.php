<?php

namespace Core\Entity;



interface FileEntityInterface extends IdentifiableEntityInterface
{
    public function setUser(EntityInterface $user);
    public function getUser();
    
    public function getAllowedUserIds();
    public function setAllowedUserIds(array $ids);
    public function addAllowedUser($user);
    
    public function getName();
    public function setName($name);
    
    public function getSize();
    public function setSize($bytes);
    
    public function getType();
    public function setType($mimeType);
    
    public function getDateUploaded();
    public function setDateUploaded(\DateTime $date);
    
    public function getContent();
    public function putContent($content);
    public function injectContent($callable);
    
    public function getResource();
    public function injectResource($callable);
    
     
}
