<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsCollection.php */
namespace Core\Form;

use Zend\Form\Element\Collection;
use Core\Entity\PermissionsInterface;

class PermissionsCollection extends Collection implements ViewPartialProviderInterface
{
    protected $partial = 'core/form/permissions-collection';
    protected $permissionsObject;
    
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    public function init()
    {
        $this->setName('permissions');
        $this->setLabel('Permissions');
        $this->setTargetElement(
            array(
            'type' => 'Core/PermissionsFieldset'
            )
        );
        $this->setCount(0);
        $this->setShouldCreateTemplate(true);
    }
    
    public function setObject($object)
    {
        if (!$object instanceof PermissionsInterface) {
            throw new \InvalidArgumentException('Object must be of type "\\Core\\Entity\\PermissionsInterface"');
        }
        
        $assigned = $object->getAssigned();
        $array    = array();
        foreach ($assigned as $resourceId => $spec) {
        }
        if (isset($assigned['groups'])) {
            foreach ($assigned['groups'] as $name => $spec) {
                $array[] = array(
                    'type' => 'group',
                    'id' => $name,
                    'permissions' => $spec['permissions']
                );
            }
        }
        if (isset($assigned['users'])) {
            foreach ($assigned['users'] as $id => $permission) {
                $array[] = array(
                    'type' => 'user',
                    'id'   => $id,
                    'permissions' => $permissions
                );
            }
        }
        
        $this->permissionsObject = $object;
        return parent::setObject($array);
    }
}
