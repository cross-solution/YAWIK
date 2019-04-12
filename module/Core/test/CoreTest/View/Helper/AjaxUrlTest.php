<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\View\Helper\AjaxUrl;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\View\Helper\AbstractHelper;

/**
 * Tests for \Core\View\Helper\AjaxUrl
 *
 * @covers \Core\View\Helper\AjaxUrl
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.View
 * @group Core.View.Helper
 */
class AjaxUrlTest extends TestCase
{
    const BASEPATH = '/this/is/the/base/path/';

    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|AjaxUrl|\ReflectionClass
     */
    private $target = [
        AjaxUrl::class,
        [self::BASEPATH],
        '@testInheritance' => [ 'as_reflection' => true ],
        '@testConstruction' => false,
    ];

    private $inheritance = [ AbstractHelper::class ];

    public function testConstruction()
    {
        $basepath = '/test/path/no/slash';
        $target   = new AjaxUrl($basepath);

        $this->assertAttributeSame($basepath . '/', 'basePath', $target);

        $basepath = '/test/path/slash/';
        $target   = new AjaxUrl($basepath);

        $this->assertAttributeSame($basepath, 'basePath', $target);
    }

    public function provideInvokationTestData()
    {
        return [
            [['name'], '?ajax=name'],
            [['another', ['test' => 'param']], '?ajax=another&test=param'],
            [[['param' => 'value', 'ajax' => 'yetagain', 'g' => 'G']], '?param=value&ajax=yetagain&g=G'],
        ];
    }

    /**
     * @dataProvider provideInvokationTestData
     *
     * @param $args
     * @param $expect
     */
    public function testInvokation($args, $expect)
    {
        $url = $this->target->__invoke(...$args);

        $expect = self::BASEPATH . $expect;
        $this->assertEquals($expect, $url);
    }

    public function testInvokationThrowsExceptionIfNoAjaxKeyIsPassed()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Key "ajax"');

        $this->target->__invoke(['no' => 'ajax-key']);
    }
}
