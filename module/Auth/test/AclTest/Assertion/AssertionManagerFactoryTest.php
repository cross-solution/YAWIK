<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace AclTest\Assertion;

use Acl\Assertion\AssertionManager;
use Acl\Assertion\AssertionManagerFactory;

/**
 * Test the AssertionManagerFactory.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AssertionManagerFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsInterface()
    {
        $target = new AssertionManagerFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $target);
    }

    public function testCreateServiceReturnsAssertionManager()
    {
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())->method('get')->with('Config')
                 ->willReturn(array());

        $target = new AssertionManagerFactory();
        $manager = $target->__invoke($services,AssertionManager::class);

        $this->assertInstanceOf('\Acl\Assertion\AssertionManager', $manager);
        //$this->assertFalse($manager->shareByDefault(), 'The managers\' shareByDefault value must be set to FALSE by the factory.');
    }

    /**
     * Note: We do not need to test the Configuration in detail here, because this is done in the test
     * for the config object in zend frameworks' unit tests already.
     *
     * @dataProvider provideConfigArrays
     */
    public function testCorrectConfigIsUsedToConfigureTheManager($config, $testName, $testResult)
    {
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())->method('get')->with('Config')
                 ->willReturn($config);

        $target = new AssertionManagerFactory();

        $manager = $target->__invoke($services,AssertionManager::class);

        $this->assertTrue($testResult === $manager->has($testName), 'Expected managers\' has method to return ' . ($testResult ? 'TRUE' : 'FALSE') . ' on ' .$testName);
    }

    public function provideConfigArrays()
    {
        return array(
            array(array('acl' => array('nono' => array('invokables' => array('failTest' => 'noneClass')))), 'failTest', false),
            array(array('acl' => array('assertions' => array('invokables' => array('successTest' => 'existClass')))), 'successTest', true),
        );
    }
}
