<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OrganizationName extends AbstractRepository
{
    public function findbyName($name, $create = true)
    {
        $entity = $this->findOneBy(array('name' => $name));
        if (empty($entity)) {
            $entity = $this->create();
            $entity->setName($name);
        }
        return $entity;
    }
}
