<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Filter;

use PHPUnit\Framework\TestCase;

use Install\Filter\DbNameExtractor;

/**
 * Tests for \Install\Filter\DbNameExtractor
 *
 * @covers \Install\Filter\DbNameExtractor
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Filter
 */
class DbNameExtractorTest extends TestCase
{
    /**
     * @testdox Extends \Zend\Filter\AbstractFilter
     */
    public function testExtendsAbstractFilter()
    {
        $this->assertInstanceOf('\Zend\Filter\AbstractFilter', new DbNameExtractor());
    }

    public function testDefinesDefaultDatabaseNameAsProperty()
    {
        $this->assertAttributeEquals('YAWIK', 'defaultDatabaseName', new DbNameExtractor());
    }

    public function providesSettingDefaultNameViaConstructorTestData()
    {
        $arr = new \ArrayObject(array('default_database_name' => 'test'));


        return array(
            array('DefaultDb', 'DefaultDb'),
            array(array('default_database_name' => 'DefDb'), 'DefDb'),
            array($arr, 'test'),
        );
    }

    /**
     * @dataProvider providesSettingDefaultNameViaConstructorTestData
     *
     * @param string|array|object $options
     * @param string $expected
     */
    public function testAllowsSettingDefaultDatabaseNameViaConstructor($options, $expected)
    {
        $target = new DbNameExtractor($options);

        $this->assertAttributeEquals($expected, 'defaultDatabaseName', $target);
    }

    public function testAllowsSettingDefaultDatabaseNameViaSetter()
    {
        $target = new DbNameExtractor();

        $target->setDefaultDatabaseName('testDb');

        $this->assertEquals('testDb', $target->getDefaultDatabaseName());
    }

    public function testExtractTheDatabaseNameFromConnectionString()
    {
        $target = new DbNameExtractor();

        $actual = $target->filter('mongodb://server/dbName');

        $this->assertEquals('dbName', $actual);
    }

    public function testReturnsDefaultDbNameUponExtractionIfNoDbNameIsSpecified()
    {
        $target = new DbNameExtractor();

        $actual = $target->filter('mongodb://server');

        $this->assertEquals('YAWIK', $actual);
    }
}
