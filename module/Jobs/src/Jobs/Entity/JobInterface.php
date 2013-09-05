<?php

namespace Jobs\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;

interface JobInterface extends EntityInterface, IdentifiableEntityInterface
{

    public function setApplyId($applyId);
    public function getApplyId();
    
    public function getApplications();
}