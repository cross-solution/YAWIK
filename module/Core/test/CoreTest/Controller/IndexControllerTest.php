<?php
/**
 * Cross Applicant Management
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
    
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);
    }
    
}

?>