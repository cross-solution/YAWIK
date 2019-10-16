<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace OrganizationsTest\Auth\Dependency;

use PHPUnit\Framework\TestCase;

use Organizations\Auth\Dependency\EmployeeListListener as ListListener;
use Zend\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Zend\View\Renderer\PhpRenderer as View;
use Organizations\Entity\OrganizationReferenceInterface;
use Organizations\Entity\OrganizationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Organizations\Entity\EmployeeInterface;
use Auth\Entity\InfoInterface;

/**
 * @coversDefaultClass \Organizations\Auth\Dependency\EmployeeListListener
 */
class EmployeeListListenerTest extends TestCase
{

    /**
     * @var ListListener
     */
    private $listListener;

    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->listListener = new ListListener();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(\Auth\Dependency\ListInterface::class, $this->listListener);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $this->assertSame($this->listListener, $this->listListener->__invoke());
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $expected = 'string';
        $translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        $translator->expects($this->once())
            ->method('translate')
            ->with($this->callback(function ($string) {
                return is_string($string);
            }))
            ->willReturn($expected);
        
        $this->assertSame($expected, $this->listListener->getTitle($translator));
    }

    /**
     * @covers ::getCount
     * @covers ::getEmployees
     * @dataProvider getUser
     */
    public function testGetCount($user, $expected)
    {
        $this->assertSame($expected, $this->listListener->getCount($user));
    }

    /**
     * @covers ::getItems
     * @covers ::getEmployees
     * @dataProvider getUser
     */
    public function testGetItems($user, $expected)
    {
        $view = $this->getMockBuilder(View::class)
            ->getMock();
        
        $actual = $this->listListener->getItems($user, $view, 10);
        
        $this->assertIsArray($actual);
        $this->assertCount($expected, $actual);
        $this->assertContainsOnlyInstancesOf(\Auth\Dependency\ListItem::class, $actual);
    }
    
    /**
     * @covers ::getEntities
     * @covers ::getEmployees
     * @dataProvider getUser
     */
    public function testGetEntities($user, $expected)
    {
        $this->assertCount($expected, $this->listListener->getEntities($user));
    }
    
    /**
     * @return array
     */
    public function getUser()
    {
        $userWithoutOrganization = $this->getMockBuilder(User::class)
            ->getMock();
        
        $userWithOrganizationWithoutReference = $this->getMockBuilder(User::class)
            ->getMock();
        $organizationWithoutReference = $this->getMockBuilder(OrganizationReferenceInterface::class)
            ->getMock();
        $userWithOrganizationWithoutReference->method('getOrganization')
            ->willReturn($organizationWithoutReference);
        
        $userWithOrganizationWithReference = $this->getMockBuilder(User::class)
            ->getMock();
        $organizationWithReference = $this->getMockBuilder(OrganizationReferenceInterface::class)
            ->getMock();
        $employee = $this->getMockBuilder(EmployeeInterface::class)
            ->getMock();
        $info = $this->getMockBuilder(InfoInterface::class)
            ->getMock();
        $employeeUser = $this->getMockBuilder(User::class)
            ->getMock();
        $employeeUser->method('getInfo')
            ->willReturn($info);
        $employee->method('getUser')
            ->willReturn($employeeUser);
        $employees = new ArrayCollection([
            $employee
        ]);
        $organization = $this->getMockBuilder(OrganizationInterface::class)
            ->getMock();
        $organization->method('getEmployees')
            ->willReturn($employees);
        $organizationWithReference->method('getOrganization')
            ->willReturn($organization);
        $userWithOrganizationWithReference->method('getOrganization')
            ->willReturn($organizationWithReference);
        
        return [
            [$userWithoutOrganization, 0],
            [$userWithOrganizationWithoutReference, 0],
            [$userWithOrganizationWithReference, 1],
        ];
    }
}
