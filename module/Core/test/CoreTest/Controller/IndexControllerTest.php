<?php
/**
 * Cross Applicant Management - Unit Tests
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace CoreTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use CoreTest\Bootstrap;

class IndexControllerTest extends AbstractControllerTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->setApplicationConfig(Bootstrap::getConfig());
    }
    
    public function testIndexActionRedirects()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(302);
        $response = $this->getResponse();
        $this->assertTrue($response->getHeaders()->has('Location'));
    }
    
    public function testIndexActionCanBeAccessed()
    {
        $this->markTestSkipped('Error in Index-View handled by other branch!');
        $this->dispatch('/en');
        $this->assertResponseStatusCode(200);
        
    }
    
}

?>