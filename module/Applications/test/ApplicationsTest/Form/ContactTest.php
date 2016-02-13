<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationsTest\Form;

use Applications\Form\ContactContainer;


/**
* @covers \Applications\Form\ContactContainer
*/
class ContactTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $target ContactContainer
     */
    protected $target;

    public function setUp(){
        $this->target = new ContactContainer();
        $this->target->init();
    }

    public function testConstructor(){
        $this->assertInstanceOf('Auth\Form\UserInfoContainer', $this->target);
        $this->assertInstanceOf('Applications\Form\ContactContainer', $this->target);
    }
    /**
     * @dataProvider providerFormActionsData
     */
    public function testFormActions($input,$expected)
    {
        $this->assertEquals($this->target->getActionFor($input),$expected);
    }

    public function providerFormActionsData(){
        return [
            ['contact','?form=contact'],
            ['image','?form=image'],
        ];
    }
}