<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\View\Helper\ContactLink as Helper;
use Zend\View\Renderer\PhpRenderer as Renderer;
use Auth\Entity\Info;

/**
 * Tests the Contact Link View Helper
 *
 * @covers \Core\View\Helper\ContactLink
 * @coversDefaultClass \Core\View\Helper\ContactLink
 * @group Core
 * @group Core.View
 * @group Core.View.Helper
 */
class ContactLinkTest extends TestCase
{
    public function testExtendsZfAbstractHelper()
    {
        $helper = new Helper;
        
        $this->assertInstanceOf('\Zend\View\Helper\AbstractHelper', $helper);
    }
    
    public function testRenderedResult()
    {
        $info = new Info;
        $helper = new Helper;
        
        $this->assertTrue(is_string($helper($info)));
        $this->assertEquals('', $helper($info));
        
        $info->setEmail('email@address.net');
        $this->assertEquals('<a href="mailto:email@address.net">email@address.net</a>', $helper($info));
        
        $info->setFirstName('Fancy');
        $info->setLastName('Name');
        $this->assertEquals('<a href="mailto:email@address.net">Fancy Name</a>', $helper($info));
        
        $view = new Renderer;
        $helper->setView($view);
        $this->assertEquals('<a class="bold" id="userContact" href="mailto:email@address.net">Fancy Name</a>', $helper($info, [
            'class' => 'bold',
            'id' => 'userContact',
        ]));
        
        $info->setEmail(null);
        $this->assertEquals('Fancy Name', $helper($info));
    }
}
