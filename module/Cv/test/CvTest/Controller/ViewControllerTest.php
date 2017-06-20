<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Controller;

use Acl\Controller\Plugin\Acl;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Controller\ViewController;
use Zend\Mvc\Controller\AbstractActionController;
use Cv\Repository\Cv as CvRepository;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Http\PhpEnvironment\Response;

/**
 * Tests for \Cv\Controller\ViewController
 *
 * @covers \Cv\Controller\ViewController
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @group Cv
 * @group Cv.Controller
 */
class ViewControllerTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|ViewController
     */
    private $target = [
        'method' => 'getSimpleTarget',
        '@testHttp404IfNoCvIsFound' => 'getTestTarget',
        '@testReturnsExpectedResult' => 'getTestTarget',
    ];

    /**
     *
     *
     * @var array
     */
    private $inheritance = [ AbstractActionController::class ];

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|CvRepository
     */
    private $repositoryMock;

    private function getSimpleTarget()
    {
        $this->repositoryMock = $this
            ->getMockBuilder(CvRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        
        $translator = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        return new ViewController($this->repositoryMock, $translator);
    }

    /**
     *
     * @return ViewController
     */
    private function getTestTarget()
    {
        $target = $this->getSimpleTarget();

        $params = $this
            ->getMockBuilder(Params::class)
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();

        $params->expects($this->once())->method('__invoke')->with('id')->willReturn(1234);

        $plugins = new PluginManager($this->getServiceManagerMock());
        $plugins->setService('params', $params);
        $target->setPluginManager($plugins);

        return $target;
    }

    public function testConstructionSetsCorrectDependencies()
    {
        $this->assertAttributeSame($this->repositoryMock, 'repository', $this->target);
    }

    public function testHttp404IfNoCvIsFound()
    {
        $this->repositoryMock->expects($this->once())->method('find')->with(1234)->willReturn(null);
        
        $this->assertArrayHasKey('message', $this->target->indexAction());
        $this->assertSame(Response::STATUS_CODE_404, $this->target->getResponse()->getStatusCode());
    }

    public function testReturnsExpectedResult()
    {
        $resume = new \Cv\Entity\Cv();
        $this->repositoryMock->expects($this->once())->method('find')->with(1234)->willReturn($resume);

        $acl = $this
            ->getMockBuilder(Acl::class)
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();

        $acl->expects($this->once())->method('__invoke')->with($resume, 'view');

        $this->target->getPluginManager()->setService('acl', $acl);

        $actual = $this->target->indexAction();
        $expected = [
            'resume' => $resume
        ];

        $this->assertEquals($expected, $actual);
    }
    
}