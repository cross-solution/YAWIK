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

use Core\Form\Form;
use Core\Form\ViewPartialProviderInterface;

/**
 * Creates search formular for job openings
 *
 * @package Jobs\Form
 */
class ListFilter extends Form implements ViewPartialProviderInterface
{

    const BASE_FIELDSET = "Jobs/ListFilterBaseFieldset";
    const LOCATION_FIELDSET = "Jobs/ListFilterLocationFieldset";
    const PERSONAL_FIELDSET = "Jobs/ListFilterPersonalFieldset";
    const ADMIN_FIELDSET = "Jobs/ListFilterAdminFieldset";

    /**
     * view script for the search formular
     *
     * @var string $viewPartial
     */
    protected $viewPartial = 'jobs/form/list-filter';

    /**
     * defines the used fieldset.
     *
     * @var string
     */
    protected $fieldset;

    /**
     * formular action.
     *
     * @var string
     */
    protected $action;

    /**
     * @param int|null|string $name
     * @param array           $options
     *
     * fieldset: string service name of the Fieldset class
     */
    public function __construct($name = null, array $options=[])
    {
        $this->fieldset = array_key_exists('fieldset',$options)?$options['fieldset']:self::BASE_FIELDSET;
        parent::__construct();
    }

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
                'options' => ['use_as_base_fieldset' => true]
            ]
        );

        $this->add(
            [
                'type' => 'Core/ListFilterButtons'
            ]
        );
    }
}