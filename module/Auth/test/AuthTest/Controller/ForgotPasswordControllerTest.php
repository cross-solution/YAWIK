<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller;

use Auth\Controller\ForgotPasswordController;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

class ForgotPasswordControllerTest extends AbstractControllerTestCase
{
    /**
     * @var MockObject
     */
    private $formMock;

    /**
     * @var MockObject
     */
    private $serviceMock;

    public function setUp()
    {
        $this->init('forgot-password');

        $this->formMock = $this->getMock('Auth\Form\ForgotPassword');

        $this->serviceMock = $this->getMockBuilder('Auth\Service\ForgotPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMock('Zend\Log\LoggerInterface');

        $this->controller = new ForgotPasswordController($this->formMock, $this->serviceMock, $loggerMock);
        $this->controller->setEvent($this->event);
    }

    public function testIndexAction_WithGetRequest()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $result = $this->controller->dispatch($request);

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);
    }

}