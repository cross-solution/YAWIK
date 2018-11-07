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

class EditorLight extends Editor implements ViewHelperProviderInterface
{
    protected $viewHelper = 'TinyMCEditorLight';
}
