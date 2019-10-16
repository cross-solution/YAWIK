<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace AuthTest\Filter;

use PHPUnit\Framework\TestCase;

use Auth\Filter\StripQueryParams;

/**
 * TestCase for \Auth\Filter\StripQueryParams.
 * @coversDefaultClass \Auth\Filter\StripQueryParams
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  Auth
 * @group  Auth.Filter
 */
class StripQueryParamsTest extends TestCase
{

    /**
     * Fixture of the class under test
     *
     * @var StripQueryParams
     */
    public $filter;

    /**
     * Setups shared fixture
     */
    protected function setUp(): void
    {
        $this->filter = new StripQueryParams();
    }

    /**
     * Tests if filter implements the FilterInterface.
     *
     * @coversNothing
     */
    public function testImplementsZfFilterInterface()
    {
        $this->assertInstanceOf('\Zend\Filter\FilterInterface', $this->filter);
    }


    /**
     * Tests if the filter provides a fluent interface.
     *
     * @coversNothing
     */
    public function testProvidesFluentInterface()
    {
        $this->assertSame(
            $this->filter,
            $this->filter->setStripParams(array()),
            'Fluent interface broken in method SetStripParams'
        );
    }

    /**
     * Tests if it's possible to set and get custom paramaters to be stripped.
     *
     * @covers ::setStripParams
     * @covers ::getStripParams
     */
    public function testAllowsSettingAndGettingCustomParametersToBeStripped()
    {
        $expected = array('custom', 'strip', 'params');

        $this->filter->setStripParams($expected);

        $this->assertEquals($expected, $this->filter->getStripParams());
    }

    /**
     * Tests if the default parameters to be stripped are returned, if no custom ones are set.
     *
     * @covers ::getStripParams
     */
    public function testUsesDefaultStripParamsListIfNotExplicitelySet()
    {
        $expected = array('logout');

        $this->assertEquals($expected, $this->filter->getStripParams());
    }

    /**
     * Data provider for testStripsQueryParametersFromUrlStrings
     *
     * @return array
     */
    public function provideUris()
    {
        return array(
            array('http://www.example.org/', null, 'http://www.example.org/'),
            array('this must be stripped?logout=1', null, 'this must be stripped'),
            array('test?q=Test&logout=YetATest&t=t', null, 'test?q=Test&t=t'),
            array('test.de?logout=no&test=data', null, 'test.de?test=data'),
            array('test.com?custom=yes&stillhere=no', array('custom'), 'test.com?stillhere=no'),
            array('someurl/?logout=yes&no=yes', array('no'), 'someurl/?logout=yes'),
            array('test?one=1&two=2&three=3&four=4', array('one', 'four'), 'test?two=2&three=3'),
        );
    }

    /**
     * Tests if the filter works as expected.
     *
     * @dataProvider provideUris
     * @covers ::filter
     *
     * @param string     $uri      The uri to filter
     * @param null|array $params   Custom parameters to be used. Passing null for using default params.
     * @param string     $expected The string which should match the filtered output.
     */
    public function testStripsQueryParametersFromUrlStrings($uri, $params, $expected)
    {
        if (null !== $params) {
            $this->filter->setStripParams($params);
        }

        $this->assertEquals($expected, $this->filter->filter($uri));
    }
}
