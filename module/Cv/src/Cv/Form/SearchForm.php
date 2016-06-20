<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Cv\Form;

use Core\Form\TextSearchForm;

class SearchForm extends TextSearchForm
{
    protected $options = [
        'button_element' => 'text',
        'placeholder' => /*@translate*/
            'search for position or company',
    ];

    protected $elementsFieldset = 'Cv/SearchFormFieldset';

    public function init()
    {
        parent::init();
    }

}