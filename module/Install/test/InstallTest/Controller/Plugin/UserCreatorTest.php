<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Controller\Plugin;

use Install\Controller\Plugin\UserCreator;
use Install\Filter\DbNameExtractor;

/**
 * Tests for \Install\Controller\Plugin\UserCreator
 * 
 * @covers \Install\Controller\Plugin\UserCreator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Controller
 * @group Install.Controller.Plugin
 */
class UserCreatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Class under test
     *
     * @var UserCreator
     */
    protected $target;

    public function setUp()
    {
        $extractor = new DbNameExtractor('YAWIK.test');
        $credentialFilter = new \Auth\Entity\Filter\CredentialFilter();

        $this->target = new UserCreator($extractor, $credentialFilter);
    }

    public function testExtendsAbstractPlugin()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', $this->target);
    }

}