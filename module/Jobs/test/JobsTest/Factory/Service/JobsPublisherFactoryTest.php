<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Service;

use Test\Bootstrap;

/**
 * Class JobsPublisherFactoryTest
 * @package Jobs\Factory\Service
 */
class JobsPublisherFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobsPublisherFactory
     */
    private $testedObj;

    /**
     *
     */
    public function setUp()
    {
        $this->testedObj = new JobsPublisherFactory();
    }

    /**
     *
     */
    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);


        $config=$sm->get('Config');
        $config['multiposting']=array("target"=> array("restServer"=> array("uri"=>"http://test.de",
            'PHP_AUTH_USER'=>'user',
            'PHP_AUTH_PW' => 'secret')));
        $sm->setService('Config',$config);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Core\Service\RestClient', $result);
    }
}