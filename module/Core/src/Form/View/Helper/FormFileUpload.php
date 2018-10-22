<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */
class FormFileUpload extends FormFile
{
    /**
     * Javascript file to inject to headscript helper
     *
     * @var string
     */
    protected $scriptFile = 'modules/Core/js/forms.file-upload.js';
    
    /**
     * @var string
     */
    protected $emptyNotice;
    
    /**
     * @var string
     */
    protected $nonEmptyNotice;
    
    /**
     * @var bool
     */
    protected $allowRemove = true;
    
    /**
     * @var bool
     */
    protected $allowClickableDropZone = true;

    public function render(ElementInterface $element)
    {
        if (!$element instanceof FileUpload) {
            throw new \InvalidArgumentException('Expects element of type "Core\Form\Element\FileUpload"');
        }

        $markup = $this->renderMarkup($element);
        $markup = str_replace('__input__', $this->renderFileElement($element), $markup);

        return $markup;
    }
    
    /**
     * @param FileUpload $element
     * @return string
     * @since 0.27
     */
    public function renderFileList(FileUpload $element)
    {
        $this->setupAssets();
        
        $file       = $element->getFileEntity();
        $preview    = '';
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();

        $template = '
<li class="fu-file fu-working">'.($this->allowRemove ? '
    <a href="#abort" class="fu-delete-button btn btn-default btn-xs">
        <span class="yk-icon yk-icon-minus"></span>
    </a>
    ' : '').'<div class="fu-progress">
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
) . '</li><li class="fu-error-general">' . $translator->translate('An unknown error occured.') . '</li>
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
        } else {
            if ($file) {
                $preview = $createFileDisplay($file);
            }
        }

        $nonemptynotice =
            '<div class="fu-nonempty-notice"' . ('' == trim($preview) ? ' style="display:none;"' : '') . '>'
            . $this->getNonEmptyNotice() . '</div>';
        $emptynotice    = '<div class="fu-empty-notice"'
                          . ('' == trim($preview) ? '' : ' style="display: none;"') . '>
                       ' . $this->getEmptyNotice() . '
                  </div>';

        $markup = '
    <span class="fu-template" data-template="%2$s"></span>
    %4$s
    <ul class="fu-files">
    %1$s
    </ul>
    %3$s';

        $markup = sprintf(
            $markup,
            $preview,
            $renderer->escapeHtmlAttr(trim($template)),
            $emptynotice,
            $nonemptynotice
        );

        return $markup;
    }
    
    /**
     * @param FileUpload $element
     * @return string
     * @throws \DomainException
     * @since 0.27
     */
    public function renderFileElement(FileUpload $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new \DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
                )
            );
        }
        
        $attributes         = $element->getAttributes();
        $attributes['type'] = $this->getType($element);
        $attributes['name'] = $name;

        return sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );
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
        $markup = '
<div class="%s" id="%s-dropzone">
    %s
   __input__
</div>';
        
        return sprintf(
            $markup,
            $this->getDropZoneClass($element),
            $element->getAttribute('id'),
            $this->renderFileList($element)
        );
    }
    
    /**
     * @param FileUpload $element
     * @return string
     * @since 0.27
     */
    public function getDropZoneClass(FileUpload $element)
    {
        return sprintf(
            'fu-dropzone fu-%s%s',
            $element->isMultiple() ? 'multiple' : 'single',
            $this->allowClickableDropZone ? '' : ' fu-non-clickable'
        );
    }
    
    /**
     * @param string $emptyNotice
     * @return FormFileUpload
     * @since 0.27
     */
    public function setEmptyNotice($emptyNotice)
    {
        $this->emptyNotice = $emptyNotice;
        
        return $this;
    }
    
    /**
     * @return string
     * @since 0.27
     */
    protected function getEmptyNotice()
    {
        if (!isset($this->emptyNotice)) {
            $this->emptyNotice = '
	            <div class="pull-left">
                    <span class="yk-icon fa-files-o fa-5x"></span>
                </div>' . $this->getDefaultNotice();
        }
        
        return $this->emptyNotice;
    }

    /**
     * @param string $nonEmptyNotice
     * @return FormFileUpload
     * @since 0.27
     */
    public function setNonEmptyNotice($nonEmptyNotice)
    {
        $this->nonEmptyNotice = $nonEmptyNotice;
        
        return $this;
    }

    /**
     * @return string
     * @since 0.27
     */
    protected function getNonEmptyNotice()
    {
        if (!isset($this->nonEmptyNotice)) {
            $this->nonEmptyNotice = $this->getDefaultNotice();
        }
        
        return $this->nonEmptyNotice;
    }

    /**
     * @return string
     * @since 0.27
     */
    protected function getDefaultNotice()
    {
        return '<small>' . $this->getTranslator()->translate('Click here to add files or use drag and drop.') . '</small>';
    }
    
    /**
     * @param boolean $allowRemove
     * @return FormFileUpload
     * @since 0.27
     */
    public function setAllowRemove($allowRemove)
    {
        $this->allowRemove = (bool)$allowRemove;
        
        return $this;
    }
    
    /**
     * @param boolean $allowClickableDropZone
     * @return FormFileUpload
     * @since 0.27
     */
    public function setAllowClickableDropZone($allowClickableDropZone)
    {
        $this->allowClickableDropZone = (bool)$allowClickableDropZone;
        
        return $this;
    }
    
    /**
     * @since 0.27
     */
    protected function setupAssets()
    {
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $basepath \Zend\View\Helper\BasePath */
        $renderer = $this->getView();
        $basepath = $renderer->plugin('basepath');
        $renderer->headscript()
            //->appendFile($basepath('assets/blueimp-file-upload/js/vendor/jquery.ui.widget.js'))
            //->appendFile($basepath('assets/blueimp-file-upload/js/jquery.iframe-transport.js'))
            //->appendFile($basepath('assets/blueimp-file-upload/js/jquery.fileupload.js'))
            ->appendFile($basepath($this->scriptFile));
    }
}
