<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileBuilder.php */ 
namespace Core\Repository\EntityBuilder;

use Core\Entity\EntityInterface;

class JsonFileBuilder extends EntityBuilder
{

    /**
     * @todo Belongs in a strategy...
     * @param EntityInterface $entity
     */
    public function unbuildd(EntityInterface $entity)
    {
        return $entity->getId();
    }
    
}
