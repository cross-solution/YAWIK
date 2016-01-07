<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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

    public function __construct()
    {
        parent::__construct();
        parent::init();
    }

    public function init()
    {

        $this->add(
            array(
                'name'    => 'l',
                'type'    => 'Location',
                'options' => array(
                    'label'       => /*@translate*/ 'Location',
                    'engine_type' => 'photon',
                ),
            )
        );

        $this->add(
            array(
                'name'    => 'd',
                'type'    => 'Zend\Form\Element\Select',
                'options' => array(
                    'label'         => /*@translate*/ 'Distance',
                    'value_options' => array(
                        '5'   => '5 km',
                        '10'  => '10 km',
                        '20'  => '20 km',
                        '50'  => '50 km',
                        '100' => '100 km'
                    ),
                ),
            )
        );
    }
}