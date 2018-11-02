<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Cv\Form;

use Cv\Entity\Location;

class SearchForm extends \Core\Form\SearchForm
{
    public function init()
    {
        $this->setAttributes([
            'id' => 'cv-list-filter',
            //'data-handle-by' => 'native',
        ]);

        $this->setOption('text_name', 'search');
        $this->setOption('text_placeholder', /*@translate*/ 'Search for resumes');
        $this->setOption('text_span', 5);

        parent::init();

        $this->setName('cv-list-filter');
        $this->setButtonElement('d');
    }

    protected function addElements()
    {
        $this->add(
             [
                 'name' => 'l',
                 'type' => 'LocationSelect',
                 'options' => [
                     'label' => /*@translate*/ 'Location',
                     'span' => 3,
                     'location_entity' => Location::class,
                 ],
                 'attributes' => [
                     'data-width' => '100%',
                 ],
             ]
        );

        $this->add(
             [
                 'name' => 'd',
                 'type' => 'Core\Form\Element\Select', //Zend\Form\Element\Select
                 'options' => [
                     'label' => /*@translate*/ 'Distance',
                     'value_options' => [
                         '5' => '5 km',
                         '10' => '10 km',
                         '20' => '20 km',
                         '50' => '50 km',
                         '100' => '100 km'
                     ],
                     'span' => 4,

                 ],
                 'attributes' => [
                     'value' => '10', // default distance
                     'data-searchbox' => -1,  // hide the search box
                     'data-allowclear' => 'false', // allow to clear a selected value
                     'data-placeholder' => /*@translate*/ 'Distance',
                     'data-width' => '100%',
                 ]
             ]
        );
    }
}
