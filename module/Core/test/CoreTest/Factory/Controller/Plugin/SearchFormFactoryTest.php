<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Core\Factory\Controller\Plugin\SearchFormFactory;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\Mock\ServiceManager\Config as ServiceManagerMockConfig;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Factory\Controller\Plugin\SearchFormFactory
 *
 * @covers \Core\Factory\Controller\Plugin\SearchFormFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Controller
 * @group Core.Factory.Controller.Plugin
 */
class SearchFormFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var \Core\Factory\Controller\Plugin\SearchFormFactory
     */
    protected $target = SearchFormFactory::class;

    protected $inheritance = [ FactoryInterface::class ];

    public function testCreatesPluginAndInjectsFormElementManager()
    {
        $forms = $this->getMockBuilder(FormElementManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        /*$services = $this->getServiceManagerMock([
            'forms' => [
                'service' => $forms,
                'count_get' => 1,
            ]]);*/
        $services = $this->getServiceManagerMock();
        $services->setService('forms', $forms);
        
        $plugin = $this->target->__invoke($services, 'irrelevant');

        $this->assertInstanceOf('\Core\Controller\Plugin\SearchForm', $plugin);
        $this->assertAttributeSame($forms, 'formElementManager', $plugin);
    }
}
