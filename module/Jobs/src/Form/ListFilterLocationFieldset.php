<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/** ListFilterLocationFieldset.php */
namespace Jobs\Form;

/**
 * Adds the location search to the base search form.
 *
 * @package Jobs\Form
 */
class ListFilterLocationFieldset extends ListFilterBaseFieldset
{

    /**
     * @var
     */
    protected $locationEngineType;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct();
        if (array_key_exists('location_engine_type', $options)) {
            $this->locationEngineType = $options['location_engine_type'];
        }
    }

    public function init()
    {
        $this->parentInit();

        $this->add(
            array(
                'name'    => 'l',
                'type'    => 'Location',
                'options' => array(
                    'label'       => /*@translate*/ 'Location',
                    'engine_type' => $this->locationEngineType,
                ),
            )
        );

        $this->add(
            array(
                'name'    => 'd',
                'type'    => 'Core\Form\Element\Select',
                'options' => array(
                    'label'         => /*@translate*/ 'Distance',
                    'value_options' => [
                        '5'   => '5 km',
                        '10'  => '10 km',
                        '20'  => '20 km',
                        '50'  => '50 km',
                        '100' => '100 km'
                    ],

                ),
                'attributes' => [
                    'value' => '10', // default distance
                    'data-searchbox'  => -1,  // hide the search box
                    'data-allowclear' => 'false', // allow to clear a selected value
                    'data-placeholder'  => /*@translate*/ 'Distance',
                ]
            )
        );
    }

    /**
     * @codeCoverageIgnore
     */
    protected function parentInit()
    {
        parent::init();
    }
}
