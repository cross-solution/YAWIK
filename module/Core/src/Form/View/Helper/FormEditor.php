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

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormTextarea;
use Zend\Json\Json;

class FormEditor extends FormTextarea
{
    /**
     * @var string
     */
    protected $theme = 'modern';

    private $skinUrl = '/dist/tinymce-skins/lightgray';

    /**
     * Default configuration of the form editor
     *
     * @see https://www.tinymce.com/docs/configure/integration-and-setup/
     * @var array
     */
    protected $options = [
        'selector' => 'div.tinymce_modern',
        'inline' => true,
        'theme' => 'modern',
        'toolbar' => 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | link | bullist | removeformat',
        'menubar' => false,
        'advlist_bullet_styles' => 'square disc',
        'block_formats' => 'Headings=h4;',
        'removed_menuitems' =>  'newdocument',
        'plugins' => 'autolink lists advlist visualblocks code fullscreen contextmenu paste link',
    ];

    /**
     * Language of tinyMCE
     *
     * @var string
     */
    protected $language="de";

    /**
     * @var
     */
    protected $translator;

    /**
     * @param ElementInterface $element
     *
     * @return string
     */
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

        if ($editorOptions = $element->getOption('editor')) {
            $this->setOptions($editorOptions);
        }
        /* @var \Zend\View\Renderer\PhpRenderer $renderer */
        $renderer = $this->getView();
        /* @var \Zend\View\Helper\HeadScript  $headscript */
        $headscript = $renderer->plugin('headscript');
        /* @var \Zend\View\Helper\BasePath  $basepath */
        $basepath = $renderer->plugin('basepath');

        //$headscript->appendFile($basepath('assets/tinymce/tinymce.min.js'));
        //$headscript->prependFile($basepath('/assets/jquery/jquery.min.js'));

        $headscript->offsetSetScript(
            '1000_tinymce_' . $this->getTheme(),
            '
            $(document).ready(function() {
            tinyMCE.init({' . $this->additionalOptions() . ',
                 skin_url: "'.$this->skinUrl.'",
                 setup:  function(editor) {
                 
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
                            ' . ($element->getOption('no-submit') ? '' : 'form.submit();').'
                            $(form).on("yk.forms.done", function(){
                                console.log("done");
                                //$(container).parents("html").removeClass("yk-changed");
                            });
                        }
                    });
                },
                init_instance_callback: function (instance)  { instance.save(); }
            });
            });'
        );

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['id']   = $name;
        $content            = $element->getValue();
        if (!isset($content)) {
            $content = '';
        }
        if (is_string($content)) {
            $class = array_key_exists('class', $attributes)?$attributes['class']:'';
            $class .= (empty($class)?:' ') . ' tinymce_' . $this->getTheme();
            $attributes['class'] = $class;
            $placeHolder = '';
            $elementOptions = $element->getOptions();
            if (array_key_exists('placeholder', $elementOptions) && !empty($elementOptions['placeholder'])) {
                $placeHolder = '<div id="placeholder-' . $name . '" style="border: 0 none; position: relative; top: 0ex; left: 10px; color: #aaa; height: 0px; overflow: visible;' .
                               (empty($content)?'':'display:none;') .
                               '">' . $this->translator->translate($elementOptions['placeholder']) . '</div>';
            }
            return
                $placeHolder
                . sprintf(
                    '<div %s >%s</div>',
                    $this->createAttributesString($attributes),
                    $content
                );
        } else {
            return (string) $content;
        }
    }

    /**
     * Gets the name of the theme
     *
     * @return string
     */
    protected function getTheme()
    {
        return $this->theme;
    }

    protected function additionalOptions()
    {
        $str = Json::encode($this->options, false, ['enableJsonExprFinder' => true]);

        return  substr($str, 1, -1);
    }

    /**
     * Translations of "Job title" and "Subtitle" are directly made in the tinymce language files
     *
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->language=$language;
    }

    /**
     * Sets the language path for tinyMCE language files
     *
     * @param $languagePath
     */
    public function setLanguagePath($languagePath)
    {
        $this->languagePath=$languagePath;
    }

    /**
     * Set a formular editor option
     *
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Set formular editor options
     *
     * @param $options
     */
    public function setOptions($options)
    {
        foreach ($options as $key => $val) {
            $this->setOption($key, $val);
        }
    }
}
