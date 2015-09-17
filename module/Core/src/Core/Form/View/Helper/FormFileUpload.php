<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/**  */
namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormFile;
use Zend\Form\ElementInterface;
use Core\Form\Element\FileUpload;

/**
 * View helper to render a file upload element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormFileUpload extends FormFile
{
    /**
     * Javascript file to inject to headscript helper
     *
     * @var string
     */
    protected $scriptFile = 'Core/js/forms.file-upload.js';


    public function render(ElementInterface $element)
    {
        if (!$element instanceof FileUpload) {
            throw new \InvalidArgumentException('Expects element of type "Core\Form\Element\FileUpload"');
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new \DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
                )
            );
        }

        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $basepath \Zend\View\Helper\BasePath */
        $renderer = $this->getView();
        $basepath = $renderer->plugin('basepath');
        $renderer->headscript()
                 ->appendFile($basepath('js/jquery-file-upload/vendor/jquery.ui.widget.js'))
                 ->appendFile($basepath('js/jquery-file-upload/jquery.iframe-transport.js'))
                 ->appendFile($basepath('js/jquery-file-upload/jquery.fileupload.js'))
                 ->appendFile($basepath($this->scriptFile));

        $markup = $this->renderMarkup($element);
        // $fileInput = parent::render($element);


        $attributes         = $element->getAttributes();
        $attributes['type'] = $this->getType($element);
        $attributes['name'] = $name;

        $fileInput = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );

        $markup = str_replace('__input__', $fileInput, $markup);

        return $markup;
    }

    /**
     * Renders the markup for the file upload element.
     *
     * @param FileUpload $element
     *
     * @return string
     */
    protected function renderMarkup(FileUpload $element)
    {
        $file       = $element->getFileEntity();
        $preview    = '';
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();

        $template = '
<li class="fu-file fu-working">
    <a href="#abort" class="fu-delete-button btn btn-default btn-xs">
        <span class="yk-icon yk-icon-minus"></span>
    </a>
    <div class="fu-progress">
        <span class="yk-icon yk-icon-spinner fa-spin"></span>
        <span class="fu-progress-text">0</span> %
    </div>
    <a class="fu-file-info" href="__file-uri__" target="_blank">
        <span class="yk-icon fa-file-o fa-4x"></span>
        __file-name__ <br> (__file-size__)
    </a>
    <div class="fu-errors input-error">
        <ul class="errors">
            <li class="fu-error-size">' . $translator->translate('The file is too big', $textDomain) . '</li>
            <li class="fu-error-type">' . $translator->translate('The file type is not supported', $textDomain) . '</li>
            <li class="fu-error-count">' . sprintf(
    $translator->translate('You may only upload %d files', $textDomain),
    $element->getAttribute('data-maxfilecount')
) . '</li>
        </ul>
   </div>
</li>';
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $basepath \Zend\View\Helper\BasePath */
        $renderer          = $this->getView();
        $basepath          = $renderer->plugin('basepath');
        $createFileDisplay = function ($file) use ($template, $basepath) {
            /* @var $file \Core\Entity\FileInterface */
            $uri  = $basepath($file->getUri());
            $name = $file->getName();
            $size = $file->getPrettySize();
            $icon = 0 === strpos($file->getType(), 'image/')
                ? 'fa-file-image-o' : 'fa-file-o';

            return str_replace(
                array('#abort',
                      '__file-uri__',
                      '__file-name__',
                      '__file-size__',
                      'fu-working',
                      'fa-file-o'
                ),
                array("$uri?do=delete", $uri, $name, $size, '', $icon),
                $template
            );

        };

        if ($element->isMultiple()) {
            if (count($file)) {
                foreach ($file as $f) {
                    $preview .= $createFileDisplay($f);
                }
            }
            $class = 'fu-multiple';
        } else {
            if ($file) {
                $preview = $createFileDisplay($file);
            }
            $class = 'fu-single';
        }

        $notice         =
            '<small>' . $translator->translate('Click here to add files or use drag and drop.') . '</small>';
        $nonemptynotice =
            '<div class="fu-nonempty-notice"' . ('' == trim($preview) ? ' style="display:none;"' : '') . '>'
            . $notice . '</div>';
        $emptynotice    = '<div class="fu-empty-notice"'
                          . ('' == trim($preview) ? '' : ' style="display: none;"') . '>
                       <div class="pull-left">
                            <span class="yk-icon fa-files-o fa-5x"></span>
                        </div>' . $notice . '
                  </div>';

        $markup = '
<div class="fu-dropzone %1$s" id="%2$s-dropzone">
    <span class="fu-template" data-template="%4$s"></span>
    %6$s
    <ul class="fu-files">
    %3$s
    </ul>
    %5$s
   __input__
</div>';

        $markup = sprintf(
            $markup,
            $class,
            $element->getAttribute('id'),
            $preview,
            $renderer->escapeHtmlAttr(trim($template)),
            $emptynotice,
            $nonemptynotice
        );

        return $markup;
    }
}
