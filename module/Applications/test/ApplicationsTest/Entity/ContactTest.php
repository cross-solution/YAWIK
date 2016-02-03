<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace AuthTest\Entity;

use Auth\Entity\Info;
use Applications\Entity\Contact;

/**
 * Tests for User
 *
 * @covers \Applications\Entity\Contact
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class ContactTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Contact
     */
    private $target;

    public function setup()
    {
        $info = new Info();
        $this->target = new Contact($info);
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Auth\Entity\UserInterface
     * @covers \Applications\Entity\Contact::__construct
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Auth\Entity\Info', $this->target);
    }
}