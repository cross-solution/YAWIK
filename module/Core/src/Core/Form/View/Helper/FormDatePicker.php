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

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormText;

/**
 * View Helper for generating the markup used by bootstrap-datepicker4 elements
 *
 * @see http://bootstrap-datepicker.readthedocs.org/en/latest/
 * @author Bleek Carsten <bleek@cross-solution.de>
 */
class FormDatePicker extends FormText
{
    /**
     * Language of the datepicker
     *
     * @var string
     */
    protected $language="de";

    public function __invoke(ElementInterface $element = null)
    {
        return $this->render($element);
    }

    public function render(ElementInterface $element = null)
    {
        /* @var \Zend\View\Renderer\PhpRenderer $view */
        $view = $this->getView();
        /* @var \Zend\View\Helper\HeadScript $headScript */
        $headScript = $view->plugin('headscript');
        /* @var \Zend\View\Helper\BasePath $basePath */

        $basePath = $view->plugin('basePath');

        if (in_array($this->language, ['de'])) {
            $headScript->appendFile($basePath('/js/bootstrap-datepicker/locales/bootstrap-datepicker.de.min.js'));
        }
        $headScript->appendFile($basePath('/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js'));


        $input = parent::render($element);

        /*
         *
         */
        $markup = '<div class="input-group date"  data-date-format="yyyy-mm-dd" data-provide="datepicker">%s<div class="input-group-addon">' .
            '<i class="fa fa-calendar"></i></div></div><div class="checkbox"></div>';
        
        $markup = sprintf(
            $markup,
            $input
        );
        
        return $markup;
    }
}
