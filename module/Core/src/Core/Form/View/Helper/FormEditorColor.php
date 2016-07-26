<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\View\Helper;

class FormEditorColor extends FormEditor
{

    /**
     * @deprecated
     * @return string
     */
    protected function additionalOptions()
    {
        return 'toolbar: "undo redo | styleselect forecolor | bold italic | alignleft aligncenter alignright alignjustify | ' .
        'bullist numlist outdent indent ", ';
    }
}
