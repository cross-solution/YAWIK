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

class FormEditorLight extends FormEditor
{
    protected $theme = 'light';

    protected $language="de";

    protected $languagePath="/js/tinymce-lang/";
    
    protected function additionalOptions() {
        return '
        toolbar: "undo redo | formatselect | alignleft aligncenter alignright | removeformat",
        menubar: false,
        block_formats: "Job title=h1;Subtitle=h2",
        '.$this->additionalLanguageOptions();
    }

    protected function additionalLanguageOptions(){
        $options='';
        if (in_array($this->language,['de','fr','it','es','hi','ar','ru','zh','tr'])) {
            $options='language: "'.$this->language.'",'.
                     'language_url: "'. $this->languagePath . $this->language.'.js",';
        }
        return $options;
    }

    /**
     * Translations of "Job title" and "Subtitle" are directly made in the tinymce language files
     *
     * @param $language
     */
    public function setLanguage($language) {
        $this->language=$language;
    }
    
     /**
     * Sets the language path for tinyMCE language files
     *
     * @param $languagePath
     */
    public function setLanguagePath($languagePath) {
        $this->languagePath=$languagePath;
    }
}
