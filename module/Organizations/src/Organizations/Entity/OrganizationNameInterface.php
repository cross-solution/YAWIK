<?php

namespace Organizations\Entity;

use Organizations\Entity\OrganizationInterface;
use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

interface OrganizationNameInterface extends EntityInterface, 
                                            IdentifiableEntityInterface
{
}