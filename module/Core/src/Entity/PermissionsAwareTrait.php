<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @property string $permissionsType
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 *
 */
trait PermissionsAwareTrait
{
    /**
     * The permissions.
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Permissions")
     * @var PermissionsInterface
     */
    private $permissions;


    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Gets the permissions entity.
     *
     * @internal
     *      If no permissions entity is set, it creates a
     *      {@link Permissions} instance and set its type to
     *      either the value provided with the property 'permissionsType' or
     *      to the entity class name, where '\\Entity\\' is replaced with '/',
     *      which leads to types such as MODULE/NAME.
     *
     *      Calls a method named 'setupPermissions' and passing the newly
     *      created permissions instance if such a method exists in the using class.
     *
     * @return PermissionsInterface
     */
    public function getPermissions()
    {
        if (!$this->permissions) {
            $type = property_exists($this, 'permissionsType')
                ? $this->permissionsType
                : str_replace('\\Entity\\', '/', static::class);
            $permissions = new Permissions($type);

            if (method_exists($this, 'setupPermissions')) {
                $this->setupPermissions($permissions);
            }

            $this->setPermissions($permissions);
        }

        return $this->permissions;
    }
}
