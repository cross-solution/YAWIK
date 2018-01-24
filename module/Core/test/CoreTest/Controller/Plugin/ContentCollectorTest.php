<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use Core\Controller\AbstractCoreController;
use Core\Controller\Plugin\ContentCollector;
use Core\EventManager\EventManager;
use Zend\EventManager\EventInterface;
use Zend\View\Model\ViewModel;

/**
 * Class ContentCollectorTest
 *
 * @covers \Core\Controller\Plugin\ContentCollector
 * @package CoreTest\Controller\Plugin
 */
class ContentCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testTriggerThrowException()
    {
        $event = $this->createMock(EventInterface::class);
        $this->expectException(\InvalidArgumentException::class);
        $target = new ContentCollector();
        $target->trigger($event);
    }

    public function testTrigger()
    {
        $event = $this->createMock(EventInterface::class);
        $events = $this->createMock(EventManager::class);

        $controller = $this->createMock(AbstractCoreController::class);
        $controller->expects($this->any())
            ->method('getEventManager')
            ->willReturn($events)
        ;

        $viewModel = new ViewModel();
        $events->expects($this->any())
            ->method('trigger')
            ->with($event,'some_target')
            ->willReturn([
                'test_template',$viewModel
            ])
        ;


        $target = new ContentCollector();
        $target->setController($controller);
        $target->setTemplate('some_template');
        $target->captureTo('some_path');

        /* @var \Zend\View\Model\ViewModel[] $childs */
        $output = $target->trigger($event,'some_target');
        $childs = $output->getChildren();
        $this->assertInstanceOf(ViewModel::class,$output);
        $this->assertEquals(
            'test_template',
            $childs[0]->getTemplate()
        );

        $this->assertEquals(
            'some_path1',
            $childs[1]->captureTo()
        );
    }
}
