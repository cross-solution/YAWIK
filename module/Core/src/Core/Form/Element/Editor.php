<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\Element;

use Zend\Form\Element\Textarea;
use Core\Service\OptionValueInterface;

class Editor extends Textarea implements ViewHelperProviderInterface
{
    protected $viewHelper = 'TinyMCEditor';

    public function setViewHelper($helper)
    {
        $this->viewHelper = $helper;
        return $this;
    }

    public function getViewHelper()
    {
        return $this->viewHelper;
    }

    public function getValue()
    {
        $value = parent::getValue();
        if ($value instanceof OptionValueInterface) {
            $value->init($this);
        }
        return $value;
    }
}
