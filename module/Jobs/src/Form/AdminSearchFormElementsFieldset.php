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

use Core\Form\SearchForm;
use Core\Form\TextSearchForm;
use Core\Form\TextSearchFormFieldset;
use Jobs\Entity\Status;

/**
 * Defines the an additional Select field for the job list filter used by the admin
 *
 * @package Jobs\Form
 */
class AdminSearchFormElementsFieldset extends SearchForm
{
    public function init()
    {
        $this->setOption('text_name', 'text');
        $this->setOption('text_label', 'Search');
        $this->setOption('text_placeholder', /*@translate*/ 'Job title');

        parent::init();

        $this->setButtonElement('text');
        $this->add(
            array(
                'type'       => 'Jobs/StatusSelect',
                'name'       => 'status',
                'options' => [
                    'include_all_option' => true,
                    'span' => 6
                ],
                'attributes' => array(
                    'value' => 'all',
                    'style' => 'width: 100%',
                    'data-submit-on-change' => "true",
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
                    'span' => 6,
                ),
                'attributes' => array(
                    'data-placeholder' => /*@translate*/ 'Select hiring organization',
                    'class' => 'form-control',
                    'style' => 'width: 100%',
                    'data-submit-on-change' => 'true',
                ),
            )
        );
    }
}
