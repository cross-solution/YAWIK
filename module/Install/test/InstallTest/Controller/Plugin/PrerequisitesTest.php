<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Install\Controller\Plugin\Prerequisites;
use org\bovigo\vfs\vfsStream;

/**
 * Tests for \Install\Controller\Plugin\Prerequisites
 *
 * @covers \Install\Controller\Plugin\Prerequisites
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Controller
 * @group Install.Controller.Plugin
 */
class PrerequisitesTest extends TestCase
{
    public function testExtendsAbstractPlugin()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', new Prerequisites());
    }

    public function testDefaultValues()
    {
        $expected = array(
            'config/autoload' => 'exists',
            'var/cache' => 'writable|creatable',
            'var/log' => 'writable|creatable',
        );

        $this->assertAttributeEquals($expected, 'directories', new Prerequisites());
    }

    public function testDirectInvokationProxiesToCorrectMethod()
    {
        $target = $this->getMockBuilder('\Install\Controller\Plugin\Prerequisites')
                       ->setMethods(array('check'))
                       ->getMock();

        /* @var $target \PHPUnit_Framework_MockObject_MockObject|Prerequisites */
        $target->expects($this->once())->method('check')->willReturn(true);

        $this->assertTrue($target());
    }

    public function testCheckCallsCheckDirectory()
    {
        $target = $this->getMockBuilder('\Install\Controller\Plugin\Prerequisites')
                       ->setMethods(array('checkDirectory'))
                       ->getMock();

        $target->expects($this->exactly(3))->method('checkDirectory')->willReturn(array('valid' => true));

        /* @var $target \PHPUnit_Framework_MockObject_MockObject|Prerequisites */

        $result = $target->check();
        $expected = array(
            'directories' => array(
                'config/autoload' => array('valid' => true),
                'var/cache' => array('valid' => true),
                'var/log' => array('valid' => true),
            ),
            'valid' => true,
        );

        $this->assertEquals($expected, $result);
    }

    public function provideCheckDirectoryTestData()
    {
        return array(
            array(array('test' => 0777), array('exists' => true, 'writable' => true, 'missing' => false, 'creatable' => false, 'valid' => true)),
            array(array('test' => false), array('exists' => false, 'writable' => false, 'missing' => true, 'creatable' => false, 'valid' => true)),
            array(array('test' => 0777, 'missing' => false), array('exists' => false, 'writable' => false, 'missing' => true, 'creatable' => true, 'valid' => true)),
            array(array('test' => 0555), array('exists' => true, 'writable' => false, 'missing' => false, 'creatable' => false, 'valid' => true)),
        );
    }

    /**
     * @dataProvider provideCheckDirectoryTestData
     *
     * @param $dir
     * @param $expected
     */
    public function testCheckDirectory($dir, $expected)
    {
        $root = vfsStream::setup('yawik', 0555);
        $parent = $root;
        $dirParts = array('yawik');
        foreach ($dir as $d => $p) {
            if ($p) {
                $directory = vfsStream::newDirectory($d, $p);
                $parent->addChild($directory);
                $parent = $directory;
            }
            $dirParts[] = $d;
        }
        $dirStr = implode(DIRECTORY_SEPARATOR, $dirParts);

        $target = $this->getMockBuilder('\Install\Controller\Plugin\Prerequisites')
                       ->setMethods(array('validateDirectory'))
                       ->getMock();

        $expectedValidateArg = $expected;
        unset($expectedValidateArg['valid']);

        $target->expects($this->once())->method('validateDirectory')
                ->with($expectedValidateArg, 'spec')
                ->willReturn($expected['valid']);

        /* @var $target \PHPUnit_Framework_MockObject_MockObject|Prerequisites */
        $result = $target->checkDirectory(vfsStream::url($dirStr), 'spec');

        $this->assertEquals($expected, $result);
    }

    public function provideValidateDirectoryTestData()
    {
        return array(
            array(array('exists' => true, 'writable' => false, 'missing' => false, 'creatable' => false), 'exists', true),
            array(array('exists' => true, 'writable' => true, 'missing' => false, 'creatable' => false), 'exists|writable', true),
            array(array('exists' => false, 'writable' => false, 'missing' => false, 'creatable' => true), 'exists|creatable', true),
            array(array('exists' => true, 'writable' => true, 'missing' => false, 'creatable' => false), 'creatable', false),
        );
    }

    /**
     * @dataProvider  provideValidateDirectoryTestData
     *
     * @param $result
     * @param $spec
     * @param $expected
     */
    public function testValidateDirectory($result, $spec, $expected)
    {
        $target = new Prerequisites();

        $expected ? $this->assertTrue($target->validateDirectory($result, $spec))
                  : $this->assertFalse($target->validateDirectory($result, $spec));
    }
}
