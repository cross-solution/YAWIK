<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory;

use PHPUnit\Framework\TestCase;

use Core\Factory\OptionsAbstractFactory;
use Zend\Stdlib\AbstractOptions;

/**
 * Tests for \Core\Factory\OptionsAbstractFactory
 *
 * @covers \Core\Factory\OptionsAbstractFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 */
class OptionsAbstractFactoryTest extends TestCase
{
    /**
     * @testdox Implements \Zend\ServiceManager\AbstractFactoryInterface
     */
    public function testImplementsAbstractFactoryInterface()
    {
        $target = new OptionsAbstractFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\AbstractFactoryInterface', $target);
    }

    /**
     * @testdox Loads its options config only once
     */
    public function testCanCreateServiceWithNameLoadsOptionsConfigOnlyOnce()
    {
        $target = new OptionsAbstractFactory();

        $services = $this->getServiceLocatorMock();

        $target->canCreateServiceWithName($services, 'justaname', 'Just.A.Name');
        $target->canCreateServiceWithName($services, 'secondrun', 'ShoulNot-Load.Again');
    }

    public function provideCanCreateServiceWithNameTestData()
    {
        $cfg1 = [
            'Test.Name/Two' => [],
            'othername' => []
        ];

        return [
            [ [], 'testnameone', 'Test/Name.One', false ],
            [ $cfg1, 'testnametwo', 'Test.Name/Two', true ],
            [ $cfg1, 'othername', '', false ],
            [ $cfg1, 'nonexistant', 'Non.Existant', false ],
        ];
    }

    /**
     * @testdox Determines if its able to create an options instance.
     * @dataProvider provideCanCreateServiceWithNameTestData
     *
     * @param $optionsConfig
     * @param $name
     * @param $requestedName
     * @param $expected
     * @deprecated This test should be removed in ZF3
     */
    public function testCanCreateServiceWithName($optionsConfig, $name, $requestedName, $expected)
    {
        $target = new OptionsAbstractFactory();

        $services = $this->getServiceLocatorMock($optionsConfig);

        $method = "assert" . ($expected ? 'True' : 'False');
        $this->$method($target->canCreateServiceWithName($services, $name, $requestedName));
    }

    public function provideCanCreateTestData()
    {
        $cfg1 = [
            'Test.Name/Two' => [],
            'othername' => []
        ];

        return [
            [ [], 'Test/Name.One', false ],
            [ $cfg1, 'Test.Name/Two', true ],
            [ $cfg1, 'othername', true ],
            [ $cfg1, 'Non.Existant', false ],
        ];
    }

    /**
     * @testdox Determines if its able to create an options instance.
     *
     * @dataProvider provideCanCreateTestData
     * @param $optionsConfig
     * @param $requestedName
     * @param $expected
     */
    public function testCanCreate($optionsConfig, $requestedName, $expected)
    {
        $target = new OptionsAbstractFactory();
        $services = $this->getServiceLocatorMock($optionsConfig);

        $method = "assert".($expected ? 'True' : 'False');
        $this->$method($target->canCreate($services, $requestedName));
    }

    /**
     * @testdox Throws exception if required config key "class" is missing
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Missing index "class" from
     */
    public function testCreateServiceWithNameThrowsExceptionIfClassKeyIsMissing()
    {
        $target = new OptionsAbstractFactory();

        $services = $this->getServiceLocatorMock([
            'TestOption' => []
        ]);

        $target->canCreateServiceWithName($services, '', '');
        $target->createServiceWithName($services, 'testoption', 'TestOption');
    }

    /**
     * @testdox Throws exception if invalid mode is specified
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown mode
     */
    public function testCreateServiceWithNameThrowsExceptionIfInvalidModeIsSpecified()
    {
        $target = new OptionsAbstractFactory();

        $services = $this->getServiceLocatorMock([
            'TestOption' => [
                'class' => __NAMESPACE__ . '\SimpleOptionsMock',
                'mode' => 'invalid',
            ]
        ]);

        $target->canCreateServiceWithName($services, '', '');
        $target->createServiceWithName($services, 'testoption', 'TestOption');
    }

    /**
     * @testdox Creates simple options instances (only skalar/array values)
     *
     */
    public function testCreatesSimpleOptionsInstance()
    {
        $optionsMockClass = __NAMESPACE__ . '\SimpleOptionsMock';
        $cfg = [
            'SimpleOptions' => [
                'class' => $optionsMockClass,
                'options' => [ 'one' => 'three' ]
            ]
        ];

        $services = $this->getServiceLocatorMock($cfg);

        $target = new OptionsAbstractFactory();

        $target->canCreateServiceWithName($services, '', '');
        $options = $target->createServiceWithName($services, 'irrelevant', 'SimpleOptions');

        $this->assertInstanceOf($optionsMockClass, $options);
        $this->assertEquals('three', $options->getOne());
        $this->assertEquals('Two', $options->getTwo());
    }

    public function testCreatesOptionsInstanceWithNumericalArray()
    {
        $cfg = [
            SimpleOptionsMock::class => [
                [
                    'one' => 'two',
                ],
            ],
        ];

        $services = $this->getServiceLocatorMock($cfg);

        $target = new OptionsAbstractFactory();

        $target->canCreateServiceWithName($services, '', '');
        $options = $target->createServiceWithName($services, 'irrelevant', SimpleOptionsMock::class);

        $this->assertEquals('two', $options->getOne());
    }

    /**
     * @testdox Creates nested options instances (values might be other options instances)
     */
    public function testCreateNestedOptionsInstance()
    {
        $nestedOptionsMockClass = __NAMESPACE__ . '\NestedOptionsMock';
        $simpleOptionsMockClass = __NAMESPACE__ . '\SimpleOptionsMock';

        $cfg = [
            'NestedOptions' => [
                'class' => $nestedOptionsMockClass,
                'mode' => OptionsAbstractFactory::MODE_NESTED,
                'options' => [
                    'skalar' => 'itsworking',
                    'array' => [ 'its' => 'working' ],
                    'opt1' => [ '__class__' => $simpleOptionsMockClass ],
                    'opt2' => [ '__class__' => $simpleOptionsMockClass, 'two' => 'four' ],
                ],
            ],
        ];

        $services = $this->getServiceLocatorMock($cfg);

        $target = new OptionsAbstractFactory();

        $target->canCreateServiceWithName($services, '', '');
        $options = $target->createServiceWithName($services, 'irrelevant', 'NestedOptions');

        $this->assertInstanceOf($nestedOptionsMockClass, $options);
        $this->assertEquals('itsworking', $options->getSkalar());
        $this->assertEquals([ 'its' => 'working' ], $options->getArray());
        $this->assertEquals('One', $options->getOpt1()->getOne());
        $this->assertEquals('four', $options->getOpt2()->getTwo());
    }

    protected function getServiceLocatorMock($optionsConfig = [])
    {
        $optionsConfig = [ 'options' => $optionsConfig ];
        $services = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        $services->expects($this->once())->method('get')->with('config')->willReturn($optionsConfig);

        return $services;
    }
}

class SimpleOptionsMock extends AbstractOptions
{
    protected $one = "One";
    protected $two= "Two";

    /**
     * @param string $one
     *
     * @return self
     */
    public function setOne($one)
    {
        $this->one = $one;

        return $this;
    }

    /**
     * @return string
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @param mixed $two
     *
     * @return self
     */
    public function setTwo($two)
    {
        $this->two = $two;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTwo()
    {
        return $this->two;
    }
}

class NestedOptionsMock extends AbstractOptions
{
    protected $skalar = 'skalar';
    protected $array = [];
    protected $opt1;
    protected $opt2;

    /**
     * @param mixed $opt1
     *
     * @return self
     */
    public function setOpt1(SimpleOptionsMock $opt1)
    {
        $this->opt1 = $opt1;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpt1()
    {
        return $this->opt1;
    }

    /**
     * @param mixed $opt2
     *
     * @return self
     */
    public function setOpt2(SimpleOptionsMock $opt2)
    {
        $this->opt2 = $opt2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpt2()
    {
        return $this->opt2;
    }

    /**
     * @param string $skalar
     *
     * @return self
     */
    public function setSkalar($skalar)
    {
        $this->skalar = $skalar;

        return $this;
    }

    /**
     * @return string
     */
    public function getSkalar()
    {
        return $this->skalar;
    }

    /**
     * @param array $array
     *
     * @return self
     */
    public function setArray($array)
    {
        $this->array = $array;

        return $this;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }
}
