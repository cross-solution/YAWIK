<?php
/**
 * YAWIK
 *
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   GPLv3
 */

namespace Organizations\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
