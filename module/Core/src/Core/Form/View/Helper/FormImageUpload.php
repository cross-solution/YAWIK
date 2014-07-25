<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form\View\Helper;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormImageUpload extends FormFileUpload
{

    protected $scriptFile = 'Core/js/forms.image-upload.js';
    
    protected function renderMarkup($element)
    {
        $file      = $element->getFileEntity();
        $preview   = '';

        if ($file) {
            $element->setAttribute('data-is-empty', false);
            if (0 === strpos($file->getType(), 'image/')) {
                $basepath  = $this->getView()->plugin('basepath');
                $preview = '<img src="' . $basepath($file->getUri()) . '" class="img-ploraid" />';
            } else {
                $preview = '<span>' . $file->getName() . '(' . $file->getPrettySize() . ')</span>';
            }
        }

        $markup = '
<div class="iu-dropzone" id="%1$s-dropzone">
    <a class="iu-delete-button btn btn-default btn-xs" id="%1$s-delete">
        <span class="yk-icon yk-icon-minus"></span>
    </a>
    <div class="iu-preview">
    %2$s
    </div>
   <div class="iu-progress">
       <span class="yk-icon yk-icon-spinner fa-spin"></span>
       <span class="iu-progress-percent">0</span>%%
   </div>

   __input__
</div>';
        
        $markup = sprintf($markup, $element->getAttribute('id'), $preview);
        return $markup;
    }
}
