<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** HistoryCollection.php */ 
namespace Applications\Entity;

use Core\Entity\CollectionInterface;
use Core\Repository\EntityBuilder\EntityBuilderInterface;

interface HistoryCollectionInterface extends CollectionInterface
{
    public function setEntityBuilder(EntityBuilderInterface $builder);
    public function getEntityBuilder();
    
    public function addFromStatus($status);
}

