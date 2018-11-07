<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Form\View\Helper;

use Core\Form\Element\FileUpload;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormImageUpload extends FormFileUpload
{
    protected $scriptFile = 'modules/Core/js/forms.image-upload.js';
    
    protected function renderMarkup(FileUpload $element)
    {
        $this->setupAssets();
        
        $file      = $element->getFileEntity();
        $preview   = '';
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();

        if ($file) {
            if (0 === strpos($file->getType(), 'image/')) {
                $basepath  = $this->getView()->plugin('basepath');
                $preview = '<img src="' . $basepath($file->getUri()) . '" class="img-polaroid" />';
            } else {
                $preview = '<span>' . $file->getName() . '(' . $file->getPrettySize() . ')</span>';
            }
        }
        $notice = '<div class="iu-empty-notice" style="padding: 10px; color: lightgrey;">
                            <div style="padding: 0px 20px 5px;"><span class="yk-icon fa-file-image-o fa-5x"></span></div>
                            <small>' . $translator->translate('Click here to add an image or use drag and drop.') . '</small>
                            
                        </div>';
        

        $markup = '
<div class="iu-wrapper" data-errors="">
<div class="iu-dropzone" id="%1$s-dropzone">
    <a class="iu-delete-button btn btn-default btn-xs" id="%1$s-delete">
        <span class="yk-icon yk-icon-minus"></span>
    </a>
    <div class="iu-preview">
    %2$s
    </div>
    %5$s
   <div class="iu-progress">
       <span class="yk-icon yk-icon-spinner fa-spin"></span>
       <span class="iu-progress-percent">0</span>%%
   </div>

   __input__
</div>
<div id="%1$s-errors" class="iu-errors input-error">
    <ul class="errors">
        <li class="iu-error-size">%3$s</li>
        <li class="iu-error-type">%4$s</li>
    </ul>
</div>
</div>';
        
        /*
         * @todo add initial error message display.
         */
        //$messages = $element->getMessages();
        
        $markup = sprintf(
            $markup,
            $element->getAttribute('id'),
            $preview,
            $translator->translate('The file is too big', $textDomain),
            $translator->translate('The file type is not supported', $textDomain),
            $notice
        );
        return $markup;
    }
}
