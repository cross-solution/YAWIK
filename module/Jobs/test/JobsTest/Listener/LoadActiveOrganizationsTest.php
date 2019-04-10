<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Listener;

use PHPUnit\Framework\TestCase;

use Core\Listener\Events\AjaxEvent;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Jobs\Listener\LoadActiveOrganizations;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationName;
use Zend\Http\PhpEnvironment\Request;
use Zend\Paginator\Paginator;

/**
 * Tests for \Jobs\Listener\LoadActiveOrganizations
 *
 * @covers \Jobs\Listener\LoadActiveOrganizations
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Listener
 */
class LoadActiveOrganizationsTest extends TestCase
{
    use SetupTargetTrait;

    /**
     *
     *
     * @var array|LoadActiveOrganizations
     */
    private $target = [
        LoadActiveOrganizations::class,
        'createPaginator',
    ];

    /**
     *
     *
     * @var Paginator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $paginatorMock;

    private function createPaginator()
    {
        $paginator = $this
            ->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCurrentPageNumber', 'setItemCountPerPage', 'getTotalItemCount', 'getIterator'])
            ->getMock();

        $this->paginatorMock = $paginator;

        return [ $paginator ];
    }

    public function testConstruction()
    {
        $this->assertAttributeSame($this->paginatorMock, 'paginator', $this->target);
    }

    public function testLoadingOrganizations()
    {
        $event = new AjaxEvent();
        $request = new Request();
        $request->getQuery()->set('page', 3);

        $event->setRequest($request);

        $org = new Organization();
        $org->setId('testOrg');
        $orgName = new OrganizationName('test');
        $org->setOrganizationName($orgName);
        $orgContact = new OrganizationContact();
        $orgContact->setCity('testCity')->setStreet('testStreet')->setHouseNumber(123);
        $org->setContact($orgContact);

        $this->paginatorMock->expects($this->once())->method('setCurrentPageNumber')->with(3)->will($this->returnSelf());
        $this->paginatorMock->expects($this->once())->method('setItemCountPerPage')->with(30);
        $this->paginatorMock->expects($this->once())->method('getIterator')->will($this->returnValue(new \ArrayIterator([
                    $org
                ])));
        $this->paginatorMock->expects($this->once())->method('getTotalItemCount')->will($this->returnValue(1));

        $expect = [
            'items' => [
                [
                    'id' => 'testOrg',
                    'text' => 'test|testCity|testStreet|123|'
                ]
            ],
            'count' => 1,
        ];

        $actual = $this->target->__invoke($event);

        $this->assertEquals($expect, $actual);
    }
}
