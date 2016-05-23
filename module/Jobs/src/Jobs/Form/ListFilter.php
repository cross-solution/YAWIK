<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ListFilter.php */
namespace Jobs\Form;

use Zend\Form\Form;
use Core\Form\ViewPartialProviderInterface;

/**
 * Creates search formular for job openings
 *
 * @package Jobs\Form
 */
class ListFilter extends Form implements ViewPartialProviderInterface
{

    /**
     * Base fieldset to use
     */
    protected $fieldset = 'Jobs/ListFilterBaseFieldset';

    /**
     * view script for the search formular
     *
     * @var string $viewPartial
     */
    protected $viewPartial = 'jobs/form/list-filter';

    /**
     * @param String $partial
     *
     * @return $this
     */
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }

    /**
     * @return string
     */
    public function getViewPartial()
    {
        return $this->viewPartial;
    }

    public function init()
    {
        $this->setName('jobs-list-filter');
        $this->setAttributes(
            [
                'id' => 'jobs-list-filter',
                'data-handle-by' => 'native'
            ]
        );

        $this->add(
            [
                'type'    => $this->fieldset,
                'options' => ['use_as_base_fieldset' => false]
            ]
        );

        $this->add(
            [
                'type' => 'Core/ListFilterButtons'
            ]
        );
    }
}
