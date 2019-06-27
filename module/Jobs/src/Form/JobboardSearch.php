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

use Core\Form\CustomizableFieldsetInterface;
use Core\Form\CustomizableFieldsetTrait;
use Core\Form\SearchForm;
use Jobs\Entity\Location;

/**
 * Adds the location search to the base search form.
 *
 * @package Jobs\Form
 */
class JobboardSearch extends SearchForm implements CustomizableFieldsetInterface
{
    use CustomizableFieldsetTrait;

    public function init()
    {
        $this->setAttribute('id', 'jobs-list-filter');
        $this->setOption('text_span', 5);
        parent::init();
        $this->setButtonElement('q');

        $this->add(
            [
                'name'       => 'l',
                'type'       => 'LocationSelect',
                'options'    => [
                    'label' => 'Location',
                    'span'  => 3,
                    'location_entity' => new Location(),
                ],
                'attributes' => [
                    'data-width' => '100%',
                ]
            ]
        );
        $this->setButtonElement('l');


        $this->add(
            array(
                'name'       => 'd',
                'type'       => 'Core\Form\Element\Select',
                'options'    => array(
                    'label'         => /*@translate*/ 'Distance',
                    'value_options' => [
                        '5'   => '5 km',
                        '10'  => '10 km',
                        '20'  => '20 km',
                        '50'  => '50 km',
                        '100' => '100 km'
                    ],
                    'span'          => 4,
                ),
                'attributes' => [
                    'value'            => '10', // default distance
                    'data-default-index' => '1',
                    'data-searchbox'   => -1,  // hide the search box
                    'data-allowclear'  => 'false', // allow to clear a selected value
                    'data-placeholder' => /*@translate*/ 'Distance',
                    'data-width'       => '100%',
                ]
            )
        );
        $this->setButtonElement('d');
    }
}
