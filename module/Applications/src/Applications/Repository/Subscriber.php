<?php

namespace Applications\Repository;

use Core\Repository\AbstractProviderRepository;
use Applications\Entity\Subscriber as Entity;

class Subscriber extends AbstractProviderRepository
{   
    
    public function findbyUri($uri, $create = false) {
        $subScriber = $this->findOneBy(array( "uri" => $uri ));
        if (!isset($subScriber) && $create) {
            $subScriber = $this->create();
            $subScriber->uri = $uri;
            $this->dm->persist($subScriber);
            $this->dm->flush();
        }
        return $subScriber; 
    }
    
    public function findbyUriOrCreate($uri) {
        return $this->findbyUri($uri, true);
    }
    
}