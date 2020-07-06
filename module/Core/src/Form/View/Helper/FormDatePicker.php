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

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormText;

/**
 * View Helper for generating the markup used by bootstrap-datepicker4 elements
 *
 * @see http://bootstrap-datepicker.readthedocs.org/en/latest/
 * @author Bleek Carsten <bleek@cross-solution.de>
 */
class FormDatePicker extends FormText
{
    const DEFAULT_DATE_FORMAT =  'yyyy-mm-dd';

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
        /* @var \Laminas\View\Renderer\PhpRenderer $view */
        $view = $this->getView();
        /* @var \Laminas\View\Helper\HeadScript $headScript */
        $headScript = $view->plugin('headscript');
        /* @var \Laminas\View\Helper\BasePath $basePath */

        $basePath = $view->plugin('basePath');
        $params   = $view->plugin('params'); /* @var \Core\View\Helper\Params $params */
        $lang     = $params('lang');
        $dataDateFormat = $element->getAttribute('data-date-format') ?? self::DEFAULT_DATE_FORMAT;

        //$headScript->appendFile($basePath('/assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js'));
        //if (in_array($this->language, ['de'])) {
        //    $headScript->appendFile($basePath('assets/bootstrap-datepicker/locales/bootstrap-datepicker.de.min.js'));
        //}

        $element->setAttributes([
            'data-date-language' => $lang,
            'data-provide' => 'datepicker',
            'data-date-format' => $dataDateFormat
        ]);
        $input = parent::render($element);

        /*
         *
         */
        $markup = '<div class="input-group date">%s<div data-toggle="description" data-target="%s" class="input-group-addon" onClick="$(this).parent().find(\':input\').datepicker(\'show\')">' .
            '<i class="fa fa-calendar"></i></div></div><div class="checkbox"></div>';
        
        $markup = sprintf(
            $markup,
            $input,
            $element->getAttribute('id')
        );
        
        return $markup;
    }
}
