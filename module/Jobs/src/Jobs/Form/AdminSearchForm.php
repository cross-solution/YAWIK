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

use Core\Form\TextSearchForm;
use Jobs\Entity\Status;

/**
 * Defines the an additional Select field for the job list filter used by the admin
 *
 * @package Jobs\Form
 */
class AdminSearchForm extends TextSearchForm
{

    protected $options = [
        'button_element' => 'text',
        'placeholder' => /*@translate*/ 'search for position or company',
    ];

    public function init()
    {
        parent::init();


        $this->add(
            array(
                'type'       => 'Jobs/StatusSelect',
                'options' => [
                    'include_all_option' => true,
                ],
                'attributes' => array(
                    'value' => 'all',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Jobs/ActiveOrganizationSelect',
                'property' => true,
                'name' => 'companyId',
                'options' => array(
                    'label' => /*@translate*/ 'Companyname',
                ),
                'attributes' => array(
                    'data-placeholder' => /*@translate*/ 'Select hiring organization',
                    'class' => 'form-control',
                ),
            )
        );
    }
}
