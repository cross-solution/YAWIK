<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use Auth\Entity\User;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Organizations\Entity\OrganizationReference;

/**
 * Test the OrganizationReference entity.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationReferenceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Are the correct interfaces implemented?
     *
     */
    public function testOrganizationReferenceImplementsInterfaces()
    {
        $target = $this->getMockBuilder('\Organizations\Entity\OrganizationReference')
                       ->disableOriginalConstructor()
                       ->getMock();

        $this->assertInstanceOf('\Organizations\Entity\OrganizationReferenceInterface', $target);
        $this->assertInstanceOf('\Organizations\Entity\OrganizationInterface', $target);
    }

    /**
     * Does loading reference work as expected?
     * Does all interface methods work as expected?
     */
    public function testLoadingOrganizationAndInterfaceImplementation()
    {
        foreach (array('owner', 'employee', 'none') as $type) {

            $organization = new Organization();

            $repository = $this->getMockBuilder('\Organizations\Repository\Organization')
                               ->disableOriginalConstructor()->getMock();

            $repository->expects($this->once())
                       ->method('findByUser')
                       ->with('1234')
                       ->willReturn('owner' == $type ? $organization : null);

            if ('owner' == $type) {
                $repository->expects($this->never())
                           ->method('findByEmployee');
            } else {
                $repository->expects($this->once())
                           ->method('findByEmployee')
                           ->with('1234')
                           ->willReturn('employee' == $type ? $organization : null);
            }

            $target = new OrganizationReference('1234', $repository);

            if ('none' == $type) {
                $this->assertNull($target->getOrganization());
                $this->assertFalse($target->hasAssociation());
                $this->assertFalse($target->isOwner());
                $this->assertFalse($target->isEmployee());
            } else {
                $this->assertSame($organization, $target->getOrganization());
                $this->assertTrue($target->hasAssociation());
                if ('owner' == $type) {
                    $this->assertTrue($target->isOwner());
                    $this->assertFalse($target->isEmployee());
                } else {
                    $this->assertFalse($target->isOwner());
                    $this->assertTrue($target->isEmployee());
                }
            }
        }
    }

    /**
     *
     * @dataProvider provideOrganizationInterfaceFunctionValues
     *
     */
    public function testOrganizationInterfaceMethodsReturnsSelfIfNoAssociation($func, $args)
    {
        $rep = $this->getMockBuilder('\Organizations\Repository\Organization')
                    ->setMethods(array('findByUser', 'findByEmployee'))
                    ->disableOriginalConstructor()->getMock();

        $target = new OrganizationReference('1234', $rep);
        if (!is_array($func)) {
            $func = array($func);
            $args = array($args);
        }

        for ($i=0,$c=count($func); $i<$c; $i+=1) {
            $f = $func[$i];
            $a = $args[$i];

            $this->assertSame($target, call_user_func_array(array($target, $f), $a));
        }
    }

    /**
     * @dataProvider provideOrganizationInterfaceFunctionValues
     *
     */
    public function testOrganizationInterfaceMethodsReturnsExpectedValues($func, $args, $expected, $assertionType = 'same')
    {
        $organization = new Organization();
        $rep = $this->getMockBuilder('\Organizations\Repository\Organization')
                    ->disableOriginalConstructor()->getMock();
        $rep->method('findByUser')->willReturn($organization);

        $target = new OrganizationReference('1234', $rep);

        if (!is_array($func)) {
            $func = array($func); $args = array($args); $expected = array($expected);
        }

        for ($i=0, $c=count($func); $i<$c; $i+=1) {
            $f = $func[$i];
            $a = $args[$i];
            $e = $expected[$i];

            if ('__self__' === $e) { $e = $target; }

            $actual = call_user_func_array(array($target, $f), $a);

            if ('instance' == $assertionType) {
                $this->assertInstanceOf($e, $actual);
            } else {
                $this->assertSame($e, $actual);
            }
        }
    }

    public function provideOrganizationInterfaceFunctionValues()
    {
        $hydrator = new EntityHydrator();
        $date = new \DateTime();
        $parent = new Organization();
        $name = new OrganizationName('noname');
        $emps = new ArrayCollection();
        $user = new User();
        $user->setId('543');
        $perms = new Permissions();

        return array(
            array(array('__set', '__get', '__isset'), array(array('id', '4321'), array('id'), array('id')), array('__self__', '4321', true)),
            array('setHydrator', array($hydrator), '__self__'),
            array('getHydrator', array(), '\Zend\Stdlib\Hydrator\HydratorInterface', 'instance'),
            array(array('setId', 'getId'), array(array('4321'), array()), array('__self__', '4321')),
            array(array('setDateCreated', 'getDateCreated'), array(array($date), array()), array('__self__', $date)),
            array(array('setDateModified', 'getDateModified'), array(array($date), array()), array('__self__', $date)),
            array(array('setParent', 'getParent'), array(array($parent), array()), array('__self__', $parent)),
            array('isHiringOrganization', array(), false),
            array(array('setOrganizationName', 'getOrganizationName'), array(array($name), array()), array('__self__', $name)),
            array(array('setDescription', 'getDescription'), array(array('nodesc'), array()), array('__self__', 'nodesc')),
            array(array('setEmployees', 'getEmployees'), array(array($emps), array()), array('__self__', $emps)),
            array(array('setUser', 'getUser', 'getPermissionsUserIds'),
                  array(array($user), array(), array()),
                  array('__self__', $user, array(PermissionsInterface::PERMISSION_ALL => array($user->getId())))
            ),
            array('getJobs', array(), null),
            array(array('setPermissions', 'getPermissions'), array(array($perms), array()), array('__self__', $perms)),
            array('getPermissionsResourceId', array(), 'organization:'),
            array('getSearchableProperties', array(), null),
            array(array('setKeywords', 'getKeywords'), array(array(array('no', 'keywords')), array()), array(null, null)),
            array('clearKeywords', array(), null),
        );
    }
}