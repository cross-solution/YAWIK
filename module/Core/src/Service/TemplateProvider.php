<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;
use Zend\Form\ElementInterface;

/**
 * provides a Value that can expand to an Template,
 * this expansion is implicit when using a string converter
 *
 *
 * Class TemplateProvider
 * @package Core\Service
 */
class TemplateProvider implements OptionValueInterface
{
    protected $value;
    protected $entity;
    protected $template;
    protected $inputFields;

    protected $serviceManager;
    protected $formElement;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function setValue($value, $entity = null)
    {
        $this->value = $value;
        $this->entity = $entity;
    }

    public function init(ElementInterface $element)
    {
        $this->formElement = $element;
        if ($element->hasAttribute('template')) {
            $this->template = $element->getAttribute('template');
        }
    }

    protected function getTemplate()
    {
        if (empty($this->template)) {
            throw new \RuntimeException('no Template defined for Formelement');
        }
        return $this->template;
    }

    public function input($value)
    {
        return $this;
    }

    public function __toString()
    {
        $viewModel = new ViewModel();
        $viewModel->setVariables(
            array(
            'entity' => $this->entity,
            'value' => $this->value,
            'element' => $this->formElement
            )
        );
        $originalView = $this->serviceManager->get('view');
        $view = clone $originalView;

        //$view->setRequest($request);
        //$view->setResponse($response);

        $viewModel->setTemplate($this->getTemplate());

        $view->render($viewModel);
        $content = $view->getResponse()->getContent();

        return $content;
    }
    
    /**
     * @param ServiceManager $serviceManager
     * @return TemplateProvider
     */
    public static function factory(ServiceManager $serviceManager)
    {
        return new static($serviceManager);
    }
}
