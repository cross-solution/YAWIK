<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Cv\Form;

use Zend\Form\Form;
use Core\Form\ViewPartialProviderInterface;

class SearchForm extends Form implements ViewPartialProviderInterface
{
    protected $fieldset = 'Cv/SearchFormFieldset';

    protected $viewPartial = 'cv/form/search.phtml';

    public function init()
    {
        $this->setName('cv-list-filter');
        $this->setAttributes([
            'id' => 'cv-list-filter',
            'data-handle-by' => 'native',
        ]);

        $this->add([
            'type' => $this->fieldset,
            'options' => [
                'use_as_base_fieldset' => false
            ]
        ]);

        
    }

    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
    }

    public function getViewPartial()
    {
        return $this->viewPartial;
    }


}