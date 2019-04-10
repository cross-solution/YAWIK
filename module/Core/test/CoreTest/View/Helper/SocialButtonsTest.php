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

use Core\View\Helper\SocialButtons;
use Auth\Options\ModuleOptions;

/**
 * Tests the Social Buttons View Helper
 *
 * @covers \Core\View\Helper\SocialButtons
 * @coversDefaultClass \Core\View\Helper\SocialButtons
 * @group Core
 * @group Core.View
 * @group Core.View.Helper
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class SocialButtonsTest extends TestCase
{
    public function testExtendsZfAbstractHelper()
    {
        $options = new ModuleOptions;
        $target = new SocialButtons($options, []);
        $this->assertInstanceOf('\Zend\View\Helper\AbstractHelper', $target);
    }
}
