<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\Permissions;
use Core\Entity\PermissionsAwareInterface;
use Core\Entity\PermissionsAwareTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\PermissionsAwareTrait
 *
 * @covers \Core\Entity\PermissionsAwareTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class PermissionsAwareTraitTest extends TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = [
        '@testSetterAndGetter|#0' => PermissionsAwareEntity::class,
        '@testSetterAndGetter|#1' => PermissionsAwareEntityWithPermissionsType::class,
        '@testSetterAndGetter|#2' => PermissionsAwareEntityWithSetupPermissions::class,
    ];

    public function propertiesProvider()
    {
        return [
            [ 'permissions', [
                'default' => new Permissions('Test/TestEntity'),
                'value'   => new Permissions('Test')

            ]],
            [ 'permissions', [
                'default' => new Permissions(PermissionsAwareEntityWithPermissionsType::TYPE),
                'value' => false,
                'ignore_setter' => true,
                'ignore_getter' => true,

            ]],
            [ 'permissions', [
                'default' => new Permissions(PermissionsAwareEntityWithSetupPermissions::TYPE),
                'value' => false,
                'ignore_setter' => true, 'ignore_getter' => true,
                'post' => function () {
                    $this->assertTrue($this->target->setupPermissionsCalled);
                },
            ]]
        ];
    }
}

class PermissionsAwareEntity implements PermissionsAwareInterface
{
    use PermissionsAwareTrait;
}

class PermissionsAwareEntityWithPermissionsType implements PermissionsAwareInterface
{
    const TYPE = 'Test/With/PermissionsType';
    use PermissionsAwareTrait;

    private $permissionsType = self::TYPE;
}

class PermissionsAwareEntityWithSetupPermissions extends PermissionsAwareEntityWithPermissionsType
{
    public $setupPermissionsCalled = false;

    protected function setupPermissions($permissions)
    {
        $this->setupPermissionsCalled = true;
    }
}
