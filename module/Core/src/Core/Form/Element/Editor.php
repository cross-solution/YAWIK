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

class Editor extends Textarea implements ViewHelperProviderInterface
{
    public function getViewHelper() {
        return new FormEditor();
    }
}