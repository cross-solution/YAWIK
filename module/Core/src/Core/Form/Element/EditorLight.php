<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\Element;

use Core\Form\View\Helper\FormEditorLight;

class EditorLight extends Editor implements ViewHelperProviderInterface
{
    protected $viewHelper = 'TinyMCEditorLight';
}
