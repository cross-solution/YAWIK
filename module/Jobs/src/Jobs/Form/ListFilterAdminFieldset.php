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

use Jobs\Entity\Status;

/**
 * Defines the an additional Select field for the job list filter used by the admin
 *
 * @package Jobs\Form
 */
class ListFilterAdminFieldset extends ListFilterBaseFieldset
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        $this->parentInit();
        $this->add(
            array(
                'type'       => 'Select',
                'name'       => 'status',
                'options'    => array(
                    'value_options' => array(
                        'all' => /*@translate*/ 'All',
                        Status::ACTIVE => /*@translate*/ 'Active',
                        Status::INACTIVE => /*@translate*/ 'Inactive',
                        //Status::WAITING_FOR_APPROVAL => /*@translate*/ 'Waiting for approval',
                        Status::CREATED => /*@translate*/ 'Created',
                        Status::PUBLISH => /*@translate*/ 'Published',
                        Status::REJECTED => /*@translate*/ 'Rejected',
                        Status::EXPIRED => /*@translate*/ 'Expired',
                    )
                ),
                'attributes' => array(
                    'value' => Status::CREATED,
                    'data-searchbox'  => -1,  // hide the search box
                    'data-allowclear' => 'false', // allow to clear a selected value
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
                ),
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
