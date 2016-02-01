<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormTextarea;

class FormEditor extends FormTextarea
{
    protected $theme = 'modern';


    protected $translator;

    public function render(ElementInterface $element)
    {
        $name   = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new \DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
                )
            );
        }

        $renderer = $this->getView();

        $headscript = $renderer->plugin('headscript');
        $basepath   = $renderer->plugin('basepath');

        $headscript->appendFile($basepath('js/tinymce/tinymce.jquery.min.js'));
        $headscript->prependFile($basepath('js/jquery.min.js'));

        $headscript->offsetSetScript(
            '1000_tinymce_' . $this->getTheme(),
            '
        $(document).ready(function() {
            tinyMCE.init({
                selector : "div.tinymce_' . $this->getTheme() . '",
                inline : true,
                theme : "modern",
                plugins: [
                    "autolink lists advlist",
                    "visualblocks code fullscreen",
                    "contextmenu paste link"
                ],
                removed_menuitems: "newdocument",' . PHP_EOL
            . $this->additionalOptions() .
            'setup: function(editor) {
                    setPlaceHolder = function(editor, show) {
                        placeHolder = $("#placeholder-" + editor.id);
                        if (placeHolder.length == 1) {
                            if (show && editor.getContent() == "") {
                                placeHolder.show();
                            }
                            else {
                                placeHolder.hide();
                            }
                         }
                    },
                    editor.on("focus", function(e) {
                        setPlaceHolder(editor, false);
                    });
                    editor.on("blur", function(e) {
                        setPlaceHolder(editor, true);
                        if (editor.isDirty()) {
                            //console.log("blur event", e);
                            editor.save();
                            var container = e.target.bodyElement;
                            $(container).parents("html").addClass("yk-changed");
                            var form = $(container).parents("form");
                            //console.log("form", form, container);
                            form.submit();
                            $(form).on("yk.forms.done", function(){
                                console.log("done");
                                //$(container).parents("html").removeClass("yk-changed");
                            });
                        }
                    });
                }
            });
        });
        '
        );

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['id']   = $name;
        $content            = $element->getValue();
        if (!isset($content)) {
            $content = '';
        }
        if (is_string($content)) {
            // content is should be in an ordinary textarea
            $escapeHtml         = $this->getEscapeHtmlHelper();

            $class = array_key_exists('class', $attributes)?$attributes['class']:'';
            $class .= (empty($class)?:' ') . ' tinymce_' . $this->getTheme();
            $attributes['class'] = $class;
            $placeHolder = '';
            $elementOptions = $element->getOptions();
            if (array_key_exists('placeholder', $elementOptions) && !empty($elementOptions['placeholder'])) {
                $placeHolder = '<div id="placeholder-' . $name . '" style="border: 0 none; position: relative; top: 0ex; left: 10px; color: #aaa; height: 0px; overflow: visible;' . (empty($content)?'':'display:none;') . '">' . $this->translator->translate($elementOptions['placeholder']) . '</div>';
            }
            return
                $placeHolder
                . sprintf(
                    '<div %s >%s</div>',
                    $this->createAttributesString($attributes),
                    $content
                );

            return sprintf(
                '<textarea %s>%s</textarea>',
                $this->createAttributesString($attributes),
                $escapeHtml($content)
            );
        } else {
            return (string) $content;
        }
    }

    protected function getTheme()
    {
        return $this->theme;
    }

    protected function additionalOptions()
    {
        return '
        toolbar: "undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | link | bullist",
        menubar: false,
        advlist_bullet_styles: "square disc",
        block_formats: "Headings=h4;",
        ';
    }
}
