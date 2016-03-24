<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form;

use Core\Form\Container;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Renderer\PhpRenderer;

/**
* @covers \Core\Form\Container
*/
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $target;

    public function setUp(){
        $this->target = new Container();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Container', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
    }

    public function testSetGetServiceLocator(){
        $input = new ServiceManager();
        $this->target->setServiceLocator($input);
        $this->assertSame($this->target->getServiceLocator(),$input);
    }

    /**
     * @dataProvider provideBool
     * @param $input
     */
    public function testSetGetIsDisableCapable($input){
        $this->target->setIsDisableCapable($input);
        $this->assertSame($this->target->isDisableCapable(),$input);
        $this->assertSame($this->target->getOption('is_disable_capable'),$input);

    }

    /**
     * @dataProvider provideBool
     * @param $input
     */
    public function testSetGetIsDisableElementCapable($input){
        $this->target->setIsDisableElementsCapable($input);
        $this->assertSame($this->target->isDisableElementsCapable(),$input);
        $this->assertSame($this->target->getOption('is_disable_elements_capable'),$input);
    }


    public function provideBool (){
        return [
            [true, true],
            [false, false],
        ];
    }

    public function testSetGetParent(){
        $input = "improve this";
        $this->target->setParent($input);
        $this->assertSame($this->target->getParent(),$input);
        $this->assertSame($this->target->hasParent(),true);
    }


    public function testRenderPrePost()
    {
        $renderer = new PhpRenderer();
        $this->assertSame($this->target->renderPost($renderer), '');
        $this->assertSame($this->target->renderPre($renderer), '');
    }

    /**
     * @param $key
     * @param $spec
     * @param $enabled
     * @dataProvider provideSetFormData
     */
    public function testSetForm($key,$spec,$enabled, $expected){
        /* @var $target Container */
        $target = $this->getMockBuilder('\Core\Form\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();
        $target->setForm($key,$spec,$enabled);
        if ($expected) {
            $this->assertAttributeContains($key,'activeForms',$target);
        }else{
            $this->assertAttributeNotContains($key,'activeForms',$target);
        }
        $this->assertAttributeSame(
            [$key => [
                'type' => $spec,
                'name' => $key,
                'entity' => '*',
                ]
            ]
            , 'forms'
            , $target);
    }


    public function provideSetFormData() {
        return [
            ['fieldname','input',true,true ],
            ['fieldname', 'foobar', false, false ],
        ];
    }

    /**
     * @param $key
     * @param $spec
     * @param $enabled
     */
    public function testSetFormWithArray(){
        $key1 = "key1";
        $key2 = "key2";
        $spec = [
            $key2 => [
                'type' => 'text',
                'name' => 'foobar'
            ]
        ];

        /* @var $target Container */
        $target = $this->getMockBuilder('\Core\Form\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();
        $target->setForm($key1,$spec,true);

        $this->assertAttributeSame(

            [$key1 => [
                $key2 => [
                    'type' => 'text',
                    'name' => 'foobar'
                ],
                'name' => $key1,
                'entity' => '*',
            ]
            ]
            , 'forms'
            , $target);
    }


}