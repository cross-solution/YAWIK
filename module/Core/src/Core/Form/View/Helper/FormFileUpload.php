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

use Zend\Form\View\Helper\FormFile;
use Zend\Form\ElementInterface;
use Core\Form\Element\FileUpload;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormFileUpload extends FormFile
{
    public function render(ElementInterface $element)
    {
        if (!$element instanceOf FileUpload) { 
            throw new \InvalidArgumentException('Expects element of type "Core\Form\Element\FileUpload"');
        }
        
        $renderer   = $this->getView();
        $basepath   = $renderer->plugin('basepath');
        $renderer->headscript()
                 ->appendFile($basepath('js/blueimp/vendor/jquery.ui.widget.js'))
                 ->appendFile($basepath('js/blueimp/jquery.iframe-transport.js'))
                 ->appendFile($basepath('js/blueimp/jquery.fileupload.js'))
                 ->appendFile($basepath('Core/js/forms.file-upload.js'));
        
        $file      = $element->getFileEntity();
        $preview   = '';
        if ($file) {
            $element->setAttribute('data-is-empty', false);
            if (0 === strpos($file->getType(), 'image/')) {
                $preview = '<img src="' . $file->getUri() . '" class="img-ploraid" />';
            } else {
                $preview = '<span>' . $file->getName() . '(' . $file->getPrettySize() . ')</span>';
            }
        }
        $fileInput = parent::render($element);
        $markup = '
<div class="fu-dropzone" id="%1$s-dropzone">
    <a class="fu-delete-button btn btn-default btn-xs" id="%1$s-delete">
        <span class="yk-icon yk-icon-minus"></span>
    </a>
    <div class="fu-preview">
    %2$s
    </div>
   <div class="fu-progress">
       <span class="yk-icon yk-icon-spinner fa-spin"></span>
       <span class="fu-progress-percent">0</span>%
   </div>

   %3$s
</div>';
        
        $markup = sprintf($markup, $element->getAttribute('id'), $preview, $fileInput);
        return $markup;
    }
}
