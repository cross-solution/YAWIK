<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Form;

use Applications\Form\Element\JobSelect;
use Core\Form\SearchForm;

/**
 * Filter form for applications list.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class ApplicationsFilter extends SearchForm
{
    protected $options = [
        'name' => 'applications_filter',
        'column_map' => [
            'q' => 3,
            'job' => 3,
            'status' => 2,
            'unread' => 3,
        ],
        'buttons_span' => 3.
    ];

    protected function addElements()
    {
        $this->add(
             [
                 'type' => JobSelect::class,
                 'name' => 'job',
                 'options' => [
                     'label' => /* @translate */ 'Enter job title',
                     //'empty_option' => 'Enter job title',
                 ],
                 'attributes' => [
                     'id' => 'job-filter',
                     'class' => 'form-control',
                     'data-placeholder' => 'Enter job title',
                     'data-autoinit' => 'false',
                     'data-submit-on-change' => 'true',
                 ]
             ]
        );

        $this->add(
             [
                 'type' => 'Applications\Form\Element\StatusSelect',
                 'name' => 'status',
                 'options' => [
                     'label' => /* @translate */ 'Status',
                 ],
                 'attributes' => [
                     'data-width' => '100%',
                     'data-submit-on-change' => 'true',
                     'data-placeholder' => /*@translate*/ 'all',
                 ]
             ]
        );

        $this->add(
             ['type' => 'ToggleButton',
                   'name' => 'unread',
                   'options' => [
                       'checked_value' => '1',
                       'unchecked_value' => '0',
                       'label' => 'unread',
                   ],
                 'attributes' => [
                     'data-submit-on-change' => 'true',
                 ]
             ]
        );

        $this->setButtonElement('unread');
    }
}
