<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Controller\Plugin;

use Zend\EventManager\EventInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;

/**
 * Class ContentCollector
 *
 * @package Core\Controller\Plugin
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ContentCollector extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $_captureTo;

    /**
     * @var string
     */
    protected $_template;

    /**
     * ContentCollector constructor.
     */
    public function __construct()
    {
        $this->_captureTo = 'content_';
    }

    /**
     * Setting the template name to use
     *
     * @param   string  $template
     * @return  $this
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Setting capture to
     *
     * @param $captureTo
     * @return $this
     */
    public function captureTo($captureTo)
    {
        $this->_captureTo = $captureTo;
        return $this;
    }

    /**
     * Trigger capture event
     *
     * @param   EventInterface $event
     * @param   mixed|null  $target
     *
     * @return  ViewModel
     */
    public function trigger($event, $target = null)
    {
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
            $viewModel->addChild($response, $this->_captureTo . $i);
        }
                
        return $viewModel;
    }
}
