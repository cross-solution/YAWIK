<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\Form;
use Zend\Form\Fieldset;

/**
* @covers \Core\Form\Form
*/
class FormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $target;

    protected function setUp(): void
    {
        $fs = new Fieldset('test', ['type'=>"text"]);
        $fs->setAttribute('class', 'myclass1 myclass2');
        $fs->setName('myField');
        $target = new Form();
        $target->add($fs);
        $this->target=$target;
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Form', $this->target);
        $this->assertInstanceOf('Zend\Form\Form', $this->target);
    }

    /**
    * @todo
    */
    /*
    public function testAdd()
    {
    }
    */

    /**
     * @covers \Core\Form\Form::isDescriptionsEnabled
     * @covers \Core\Form\Form::setIsDescriptionsEnabled
     */
    public function testSetGetIsDescriptionEnabled()
    {
        $input = true;
        $this->target->setIsDescriptionsEnabled($input);
        $this->assertEquals($this->target->isDescriptionsEnabled(), $input);
        $input = false;
        $this->target->setIsDescriptionsEnabled($input);
        $this->assertEquals($this->target->isDescriptionsEnabled(), $input);
    }

    /**
     * @covers \Core\Form\Form::setDescription
     */
    public function testSetDescription()
    {
        $input = "this is my description";
        $this->target->setDescription($input);
        $this->assertAttributeSame(['description' => $input,
                                    'description_params' => null], 'options', $this->target);
    }

    public function testSetDescriptionWithAdditionalParams()
    {
        $input = "this is my description";
        $this->target->setDescription($input, ['p1','p2']);
        $this->assertAttributeSame(['description' => $input,
                                    'description_params' => ['p1','p2']], 'options', $this->target);
    }

    /**
     * @covers \Core\Form\Form::setIsDisableCapable
     * @covers \Core\Form\Form::isDisableCapable
     */
    public function testSetGetIsDisableCapable()
    {
        $input = true;
        $this->target->setIsDisableCapable($input);
        $this->assertAttributeSame(['is_disable_capable' => $input], 'options', $this->target);
        $this->assertEquals($this->target->isDisableCapable(), $input);
    }

    /**
     * @covers \Core\Form\Form::setIsDisableElementsCapable
     * @covers \Core\Form\Form::isDisableElementsCapable
     */
    public function testSetGetIsDisableElementsCapable()
    {
        $input = true;
        $this->target->setIsDisableElementsCapable($input);
        $this->assertAttributeSame(['is_disable_elements_capable' => $input], 'options', $this->target);
        $this->assertEquals($this->target->isDisableElementsCapable(), $input);
    }

    /**
    * @todo
    */
    /*
    public function testDisableElement()
    {    }
    */

    /**
     * @covers \Core\Form\Form::setOptions
     */
    public function testSetOptions()
    {
        $description        = 'my description';
        $enableDescriptions = true;

        $options = ['description' => $description, 'enable_descriptions' => $enableDescriptions];
        $this->target->setOptions($options);
        $this->assertAttributeSame(
            [
                'description'         => $description,
                'enable_descriptions' => $enableDescriptions
            ],
            'options',
            $this->target
        );
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideAddClassData
     * @covers \Core\Form\Form::addClass
     *
     * @param string $class     the expected name for the status
     * @param string $expected  the expected order for the status
     */
    public function testAddClass($classes, $expected)
    {
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->target->addClass($class);
            }
        } elseif (is_string($classes)) {
            $this->target->addClass($classes);
        }

        $x=$this->target->getAttribute('class');
        $this->assertEquals($x, $expected);
    }


    public function provideAddClassData()
    {
        return [
            [['class1', 'class2'], 'class1 class2'],
            ['class2', 'class2'],
        ];
    }
}
