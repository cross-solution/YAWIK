<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\Element;

use Zend\Form\Element\Textarea;
use Core\Form\View\Helper\FormEditor;
use Core\Form\Element\ViewHelperProviderInterface;
use Core\Service\OptionValueInterface;

class Editor extends Textarea implements ViewHelperProviderInterface
{
    protected $viewHelper;

    public function setViewHelper($helper) {
        $this->viewHelper = $helper;
        return $this;
    }

    public function getViewHelper() {
        if (!isset($this->viewHelper)) {
            $this->setViewHelper(new FormEditor());
        }
        return $this->viewHelper;
    }

    public function getValue() {
        $value = parent::getValue();
        if ($value instanceof OptionValueInterface) {
            $value->init($this);
        }
        return $value;
    }
}