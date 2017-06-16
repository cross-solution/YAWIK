<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace JobsTest\Listener;

use Jobs\Listener\Publisher;

/**
 * Class PublisherTest
 *
 * @author Mathias Weitz
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author fedys
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @package JobsTest\Listener
 */
class PublisherTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Publisher
     */
    protected $target;

    /**
     * @var
     */
    protected $jobEvent;

    /**
     * @var
     */
    protected $job;

    protected $repositories;

    /**
     * @var
     */
    protected $serviceManager;

    /**
     * @var
     */
    protected $restClient;

    protected $organization;

    protected $provider;

    protected $viewPhpRendererStrategy;

    protected $renderer;

    protected $viewModel;

    protected $response;

    protected $publisher;

    protected $providerChannel;

    protected $filterManager;

    protected $htmlAbsPathFilter;

    /**
     * @var
     */
    protected $templateFilter;


    public static $reference;
    public static $externalId;

    public static function providerChannelGetter($attribute)
    {
        return strtolower($attribute);
    }


    public static function publisherSetter($attribute, $value)
    {
        if (in_array($attribute, array('reference', 'externalId'))) {
            self::${$attribute} = $value;
        }
        return strtolower($attribute);
    }

    public static function absPathFilter($value)
    {
        return $value;
    }

    /**
     *
     */
    public function setUp()
    {
        $this->serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->target = new Publisher($this->serviceManager);

        $staticClassPrefix = '\\' . __CLASS__ . '::';

        $this->log = $this->getMockBuilder('\Zend\Log\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->renderer = $this->getMockBuilder('\Zend\View\Renderer\PhpRenderer')
                ->disableOriginalConstructor()
                ->getMock();

        $this->renderer->expects($this->once())
                ->method('render')
                ->will($this->returnValue('<html />'));

        $this->viewPhpRendererStrategy = $this->getMockBuilder('\Zend\View\Strategy\PhpRendererStrategy')
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->viewPhpRendererStrategy->expects($this->once())
                          ->method('getRenderer')
                          ->will($this->returnValue($this->renderer));

        $this->response = $this->getMockBuilder('\Zend\Http\Response')
                                              ->disableOriginalConstructor()
                                              ->getMock();

        $this->jobEvent = $this->getMockBuilder('\Jobs\Listener\Events\JobEvent')
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->job = $this->getMockBuilder('\Jobs\Entity\Job')
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->publisher = $this->getMockBuilder('\Jobs\Entity\Publisher')
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->publisher->expects($this->at(0))
                           ->method('__get')
                           ->with('externalId')
                           ->will($this->returnValue('externalId32'));

        $this->publisher->expects($this->at(1))
                           ->method('__get')
                           ->with('reference')
                           ->will($this->returnValue('reference32'));

        $this->publisher->expects($this->any())
                              ->method('__set')
                              ->will($this->returnCallback($staticClassPrefix . 'publisherSetter'));

        $this->htmlAbsPathFilter = $this->getMockBuilder('\Core\Filter\HtmlAbsPathFilter')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->htmlAbsPathFilter->expects($this->any())
                              ->method('filter')
                              ->will($this->returnCallback($staticClassPrefix . 'absPathFilter'));

        $this->filterManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $this->filterManager->expects($this->at(0))
                        ->method('get')
                        ->willReturn($this->htmlAbsPathFilter);

        $this->restClient = $this->getMockBuilder('\Core\Service\RestClient')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        //$this->restClient->expects($this->at(0))
        //                   ->method('getHost')
        //                   ->will($this->returnValue('host32'));

        $this->restClient->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('host32'));


        $this->restClient->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->organization = $this->getMockBuilder('\Organizations\Entity\Organization')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->organization->expects($this->at(0))
                        ->method('__get')
                        ->with('name')
                        ->will($this->returnValue('name32'));

        $this->provider = $this->getMockBuilder('\Jobs\Options\ProviderOptions')
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->providerChannel = $this->getMockBuilder('\Jobs\Options\ChannelOptions')
                                      ->disableOriginalConstructor()
                                      ->getMock();

        $this->providerChannel->expects($this->any())
                              ->method('__get')
                              ->will($this->returnCallback($staticClassPrefix . 'providerChannelGetter'));

        $this->provider->expects($this->any(0))
                       ->method('__get')
                       ->with('channels')
                       ->willReturn(array('bbb value' => $this->providerChannel));

        $this->viewModel = $this->getMockBuilder('\Zend\View\Model\ViewModel')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->templateFilter = $this->getMockBuilder('\Jobs\Factory\Filter\ViewModelTemplateFilterFactory')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->templateFilter->expects($this->once())
                        ->method('getModel')
                        ->will($this->returnValue($this->viewModel));

        $this->job
            ->expects($this->once())
            ->method('getPublisher')
            ->with('host32')
            ->will($this->returnValue($this->publisher));

        $this->job->expects($this->at(1))->method('__get')->with('organization')->willReturn($this->organization);
        $this->job->expects($this->at(2))->method('__get')->with('title')->willReturn('title');

        $this->job->expects($this->at(7))->method('__get')->with('portals')->willReturn(array('bbb key' => 'bbb value'));

        $this->jobEvent->expects($this->once())
            ->method('getJobEntity')
            ->will($this->returnValue($this->job));

        $this->serviceManager
            ->expects($this->at(0))
            ->method('has')
            ->with('Jobs/RestClient')
            ->will($this->returnValue(true));

        $this->serviceManager
            ->expects($this->at(1))
            ->method('get')
            ->with('Core/Log')
            ->will($this->returnValue($this->log));

        $this->serviceManager
            ->expects($this->at(2))
            ->method('get')
            ->with('Jobs/RestClient')
            ->will($this->returnValue($this->restClient));

        $this->serviceManager
            ->expects($this->at(3))
            ->method('get')
            ->with('Jobs/Options/Provider')
            ->will($this->returnValue($this->provider));

        $this->serviceManager
            ->expects($this->at(4))
            ->method('get')
            ->with('ViewPhpRendererStrategy')
            ->will($this->returnValue($this->viewPhpRendererStrategy));

        $this->serviceManager
            ->expects($this->at(5))
            ->method('get')
            ->with('Jobs/ViewModelTemplateFilter')
            ->will($this->returnValue($this->templateFilter));

        $this->serviceManager
            ->expects($this->at(6))
            ->method('get')
            ->with('filterManager')
            ->will($this->returnValue($this->filterManager));

        $this->serviceManager
            ->expects($this->at(7))
            ->method('get')
            ->with('repositories')
            ->will($this->returnValue($this->repositories));
    }

    protected function setRestReturn($referenceUpdate, $applyIdUpdate)
    {
        $this->response->expects($this->any())
                       ->method('getBody')
                       ->will($this->returnValue(json_encode(
                           array(
                               'referenceUpdate' => $referenceUpdate,
                               'applyIdUpdate' => $applyIdUpdate
                           ))));
    }


    /**
     * Tests if a different applyId and reference is saved
     */
    public function testRestReturn()
    {
        $this->setRestReturn('aaa', 'bbb');
        $response = $this->target->restPost($this->jobEvent);
        $this->assertEquals('aaa', self::$reference);
        $this->assertEquals('bbb', self::$externalId);
    }
}
