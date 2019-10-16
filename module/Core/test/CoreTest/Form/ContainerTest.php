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

use Applications\Entity\Application;
use Auth\Entity\User;
use Core\Form\Container;
use Jobs\Entity\Job;
use Organizations\Entity\Organization;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Renderer\PhpRenderer;

/**
* @covers \Core\Form\Container
*/
class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new Container();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(\Zend\Form\Element::class, $this->target);
        $this->assertInstanceOf(\IteratorAggregate::class, $this->target);
        $this->assertInstanceOf(\Countable::class, $this->target);
        $this->assertInstanceOf(\Core\Form\DisableElementsCapableInterface::class, $this->target);
        $this->assertInstanceOf(\Core\Form\FormParentInterface::class, $this->target);
    }

    public function testSetFormElementManager()
    {
        $input = new ServiceManager();
        $this->assertSame($this->target, $this->target->setFormElementManager($input));
    }

    /**
     * @dataProvider provideBool
     * @param $input
     */
    public function testSetGetIsDisableCapable($input)
    {
        $this->target->setIsDisableCapable($input);
        $this->assertSame($this->target->isDisableCapable(), $input);
        $this->assertSame($this->target->getOption('is_disable_capable'), $input);
    }

    /**
     * @dataProvider provideBool
     * @param $input
     */
    public function testSetGetIsDisableElementCapable($input)
    {
        $this->target->setIsDisableElementsCapable($input);
        $this->assertSame($this->target->isDisableElementsCapable(), $input);
        $this->assertSame($this->target->getOption('is_disable_elements_capable'), $input);
    }

    public function provideBool()
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    /**
     * @covers \Core\Form\Container::setParent()
     * @covers \Core\Form\Container::getParent()
     */
    public function testSetGetParent()
    {
        $input = "improve this";
        $this->target->setParent($input);
        $this->assertSame($this->target->getParent(), $input);
        $this->assertSame($this->target->hasParent(), true);
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
    public function testSetForm($key, $spec, $enabled, $expected)
    {
        /* @var $target Container */
        $target = $this->getMockBuilder('\Core\Form\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();
        $target->setForm($key, $spec, $enabled);
        if ($expected) {
            $this->assertAttributeContains($key, 'activeForms', $target);
        } else {
            $this->assertAttributeNotContains($key, 'activeForms', $target);
        }
        $this->assertAttributeSame(
            [$key => [
                'type' => $spec,
                'name' => $key,
                'entity' => '*',
                ]
            ],
            'forms',
            $target
        );
    }


    public function provideSetFormData()
    {
        return [
            ['fieldname','input',true,true ],
            ['fieldname', 'foobar', false, false ],
        ];
    }

    public function testSetFormWithArray()
    {
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
        $target->setForm($key1, $spec, true);

        $this->assertAttributeSame(
            [$key1 => [
                $key2 => [
                    'type' => 'text',
                    'name' => 'foobar'
                ],
                'name' => $key1,
                'entity' => '*',
            ]
            ],
            'forms',
            $target
        );
    }

    /**
     * @dataProvider provideEntities
     */
    public function testSetGetEntity($input, $key, $expected)
    {
        $this->target->setEntity($input, $key, $expected);
        $this->assertEquals($expected, $this->target->getEntity($key));
    }

    public function provideEntities()
    {
        $e = [
            'application' => new Application(),
            'job' => new Job(),
            'user' => new User(),
            'organization' => new Organization(),
            ];


        return [
            [$e['application'], null , $e['application'] ],
            [$e['job'], 'job' , $e['job'] ],
            [$e['user'], null , $e['user'] ],
            [$e['organization'], null , $e['organization'] ],
        ];
    }
    
    public function testFormatAction()
    {
        $this->assertSame('', $this->target->formatAction(null));
        
        $name = 'name';
        $this->assertSame($name, $this->target->formatAction($name));
    }
    
    public function testFormatActionWithParent()
    {
        $name = 'name';
        $childName = 'child';
        $child = new Container($childName);
        $this->target->setForm($childName, $child);
        $this->assertSame($childName .'.' . $name, $child->formatAction($name));
    }
    
    public function testGetActionFor()
    {
        $this->assertNull($this->target->getActionFor('non-existent'));
        
        $key = 'name';
        $this->target->setForm($key, []);
        $this->assertSame($this->target->getActionFor($key), sprintf('?form=%s', $key));
    }
}
