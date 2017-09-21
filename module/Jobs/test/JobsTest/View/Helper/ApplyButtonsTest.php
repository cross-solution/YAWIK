<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author fedys
 * @license   AGPLv3
 */
namespace JobsTest\View\Helper;

use Jobs\View\Helper\ApplyButtons as Helper;
use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Model\ViewModel;
use Zend\View\Helper\ViewModel as ViewModelHelper;

class ApplyButtonsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Helper
     */
    private $helper;
    
    /**
     * @var View
     */
    private $view;
    
    /**
     * @var ViewModel
     */
    private $viewModel;

    public function setUp()
    {
        $this->viewModel = new ViewModel();
        $this->viewModel->setTemplate('test/template');

        $viewModelHelper = $this->getMockBuilder(ViewModelHelper::class)
            ->setMethods(['getCurrent'])
            ->getMock();
        $viewModelHelper->expects($this->any())
            ->method('getCurrent')
            ->willReturn($this->viewModel);

        $this->view = $this->getMockBuilder(View::class)
            ->setMethods(['viewModel', 'translate', 'partial', 'url'])
            ->getMock();
        $this->view->expects($this->any())
            ->method('viewModel')
            ->willReturn($viewModelHelper);
        
        $this->helper = new Helper();
        $this->helper->setView($this->view);
    }
    
    /**
     * @dataProvider invalidDataThrowsExceptionData
     */
    public function testInvalidDataReturnsEmptyString($data)
    {
        $helper = $this->helper;
		$this->assertSame('', $helper($data));
    }
    
    public function testDefaults()
    {
        $this->view->expects($this->once())
            ->method('partial')
            ->with(dirname($this->viewModel->getTemplate()) . '/' . $this->helper->getPartial(),
                    $this->callback(function(array $variables)
                    {
                        return $variables['default'] && $variables['oneClick'] === [];
                    }));
        
        $helper = $this->helper;
        $helper($this->validData());
    }
    
    public function testPartialOption()
    {
        $options = [
            'partial' => 'my/custom/partial'
        ];
        
        $this->view->expects($this->once())
            ->method('partial')
            ->with(dirname($this->viewModel->getTemplate()) . '/' . $options['partial']);
        
        $helper = $this->helper;
        $helper($this->validData(), $options);
    }
    
    public function testOneClickOnlyOption()
    {
        $options = [
            'oneClickOnly' => true
        ];
        
        $this->view->expects($this->once())
            ->method('partial')
            ->with($this->anything(),
                    $this->callback(function(array $variables)
                    {
                        return $variables['default'] === null;
                    }));
        
        $helper = $this->helper;
        $helper($this->validData(), $options);
    }
    
    public function testDefaultLabelOption()
    {
        $options = [
            'defaultLabel' => 'My default label'
        ];
        
        $this->view->expects($this->once())
            ->method('partial')
            ->with($this->anything(),
                    $this->callback(function(array $variables) use ($options)
                    {
                        return $variables['default']['label'] === $options['defaultLabel'];
                    }));
        
        $helper = $this->helper;
        $helper($this->validData(), $options);
    }
    
    public function testOneClickLabelOption()
    {
        $data = $this->validData();
        $data['oneClickProfiles'] = ['some', 'another'];
        $options = [
            'oneClickLabel' => 'My one click label with %s'
        ];
        
        $this->view->expects($this->once())
            ->method('partial')
            ->with($this->anything(),
                    $this->callback(function(array $variables) use ($options)
                    {
                        foreach ($variables['oneClick'] as $button)
                        {
                            if ($button['label'] !== sprintf($options['oneClickLabel'], $button['network']))
                            {
                                return false;
                            }
                        }
                        
                        return true;
                    }));
        
        $helper = $this->helper;
		$helper($data, $options);
    }
    
    public function testSendImmediatelyOption()
    {
        $data = $this->validData();
        $data['oneClickProfiles'] = ['some', 'another'];
        $options = [
            'sendImmediately' => true
        ];
        
        $this->view->expects($this->exactly(count($data['oneClickProfiles'])))
            ->method('url')
            ->with('lang/apply-one-click',
                    $this->callback(function(array $variables) use ($options)
                    {
                        return $variables['immediately'] === $options['sendImmediately'];
                    }));
        
        $helper = $this->helper;
		$helper($data, $options);
    }
    
    public function invalidDataThrowsExceptionData()
    {
        return [
            [
                []
            ],
            [
                [
                    'uri' => 'some'
                ]
            ],
            [
                [
                    'oneClickProfiles' => []
                ]
            ],
            [
                [
                    'uri' => 'some',
                    'oneClickProfiles' => []
                ]
            ],
            [
                [
                    'applyId' => 'some'
                ]
            ],
            [
                [
                    'applyId' => 'some',
                    'oneClickProfiles' => []
                ]
            ],
            [
                [
                    'uri' => 'some',
                    'applyId' => 'some'
                ]
            ]
        ];
    }
    
    protected function validData()
    {
        return [
            'applyId' => 'someid',
            'oneClickProfiles' => [],
            'uri' => 'someuri'
        ];
    }
}
