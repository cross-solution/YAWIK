<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Listener;

use PHPUnit\Framework\TestCase;

use Applications\Listener\JobSelectValues;
use Applications\Paginator\JobSelectPaginator;
use Core\Listener\Events\AjaxEvent;
use Jobs\Entity\Job;
use Zend\Http\PhpEnvironment\Request;

/**
 * Tests for \Applications\Listener\JobSelectValues
 *
 * @covers \Applications\Listener\JobSelectValues
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Listener
 */
class JobSelectValuesTest extends TestCase
{
    private $target;
    private $paginator;

    protected function setUp(): void
    {
        $this->paginator = $this->getMockBuilder(JobSelectPaginator::class)->disableOriginalConstructor()->getMock();
        $this->target    = new JobSelectValues($this->paginator);
    }

    public function testConstruction()
    {
        $this->assertAttributeSame($this->paginator, 'paginator', $this->target);
    }

    public function testInvokation()
    {
        $event = new AjaxEvent();
        $request = new Request();
        $query = $request->getQuery();
        $query->set('q', 'test');
        $query->set('page', 5);

        $event->setRequest($request);
        $job = new Job();
        $job->setId('test')->setTitle('testTitle');
        $jobs = [$job];
        $jobsIterator = new \ArrayIterator($jobs);

        $this->paginator->expects($this->once())->method('search')->with('test')->will($this->returnSelf());
        $this->paginator->expects($this->once())->method('setCurrentPageNumber')->with(5)->will($this->returnSelf());
        $this->paginator->expects($this->once())->method('setItemCountPerPage')->with(30);
        $this->paginator->expects($this->once())->method('getTotalItemCount')->will($this->returnValue(100));
        $this->paginator->expects($this->once())->method('getIterator')->will($this->returnValue($jobsIterator));

        $expected = [
            'items' => [
                ['id' => 0, 'text' => ''],
                ['id' => 'test', 'text' => 'testTitle'],
            ],
            'count' => 100
        ];

        $this->assertEquals($expected, $this->target->__invoke($event));
    }
}
