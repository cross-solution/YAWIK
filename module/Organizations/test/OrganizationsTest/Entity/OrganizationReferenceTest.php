<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationName;
use Organizations\Entity\OrganizationReference;
use Organizations\Entity\Template;
use Organizations\Entity\WorkflowSettings;

/**
 * Test the OrganizationReference entity.
 *
 * @covers \Organizations\Entity\OrganizationReference
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationReferenceTest extends TestCase
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
        //$this->markTestSkipped('must be revisited. https://github.com/cross-solution/YAWIK/issues/348 ');

        $organization = new Organization();
        $rep = $this->getMockBuilder('\Organizations\Repository\Organization')
                    ->disableOriginalConstructor()->getMock();
        $rep->method('findByUser')->willReturn($organization);

        $target = new OrganizationReference('1234', $rep);

        if (!is_array($func)) {
            $func = array($func);
            $args = array($args);
            $expected = array($expected);
        }

        for ($i=0, $c=count($func); $i<$c; $i+=1) {
            $f = $func[$i];
            $a = $args[$i];
            $e = $expected[$i];

            if ('__self__' === $e) {
                $e = $target;
            }

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
        $user->setRole('department manager');
        $perms = new Permissions();
        $contact = new OrganizationContact();
        $image = new OrganizationImage();
        $externalId='myReference';
        $template = new Template();
        $template->setLabelBenefits('These are your Benefits');
        $workflowSettings = new WorkflowSettings();
        $workflowSettings->setAcceptApplicationByDepartmentManager(true);

        return [
            ['setHydrator', [$hydrator], '__self__'],
            ['getHydrator', [], '\Zend\Hydrator\HydratorInterface', 'instance'],
            [['setId', 'getId'], [['4321'], []], ['__self__', '4321']],
            [['setContact', 'getContact'], [[$contact], []], ['__self__', $contact]],
            [['setDateCreated', 'getDateCreated'], [[$date], []], ['__self__', $date]],
            [['setDateModified', 'getDateModified'], [[$date], []], ['__self__', $date]],
            [['setExternalId', 'getExternalId'], [[$externalId], []], ['__self__', $externalId]],
            [['setParent', 'getParent'], [[$parent], []], ['__self__', $parent]],
            [['setImage', 'getImage'], [[$image], []], ['__self__', $image]],
            [['setTemplate', 'getTemplate'], [[$template], []], ['__self__', $template]],
            [['setWorkflowSettings', 'getWorkflowSettings'], [[$workflowSettings], []], ['__self__', $workflowSettings]],
            ['isHiringOrganization', [], false],
            ['getHiringOrganizations', [], null],
            [['setOrganizationName', 'getOrganizationName'], [[$name], []], ['__self__', $name]],
            [['setDescription', 'getDescription'], [['nodesc'], []], ['__self__', 'nodesc']],
            [['setEmployees', 'getEmployees'], [[$emps], []], ['__self__', $emps]],
            ['getEmployee', ['4321'], null],
            [['setUser', 'getUser', 'getPermissionsUserIds'],
                  [[$user], [], []],
                  ['__self__', $user, [PermissionsInterface::PERMISSION_ALL => [$user->getId()]]]
            ],
            ['getJobs', [], null],
            [['setPermissions', 'getPermissions'], [[$perms], []], ['__self__', $perms]],
            ['getPermissionsResourceId', [], 'organization:'],
            ['getSearchableProperties', [], []],
        ];
    }
}
