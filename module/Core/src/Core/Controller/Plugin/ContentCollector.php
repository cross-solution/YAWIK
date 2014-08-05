<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
//use Zend\Stdlib\DispatchableInterface as Dispatchable;
//use Zend\EventManager\Event;
//use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

class ContentCollector extends AbstractPlugin {

    protected $_captureTo;
    protected $_template;
    
    public function __construct() {
        $this->_captureTo = 'content_';
    }
    
    public function setTemplate($template) {
        $this->_template = $template;
        return $this;
    }
    
    public function captureTo($captureTo) {
        $this->_captureTo = $captureTo;
        return $this;
    }
    
    public function trigger($event, $target = null) {
        if (empty($this->_template) || !is_string($this->_template)) {
              throw new \InvalidArgumentException('ContentCollector must have a template-name');
        } 
          
        $responseCollection = $this->getController()->getEventManager()->trigger($event, $target);
        $viewModel = new ViewModel();
        $viewModel->setTemplate($this->_template);
        foreach ($responseCollection as $i => $response) {
              if (is_string($response)) {
                        $template = $response;
                        $response = new ViewModel(array('target' => $target));
                        $response->setTemplate($template);
                    }
                    $viewModel->addChild($response,  $this->_captureTo . $i);
                }
                
        return $viewModel;
    }
    
}