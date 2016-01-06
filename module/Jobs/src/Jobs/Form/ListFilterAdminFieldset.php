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

use Jobs\Entity\Status;
use Zend\Form\Fieldset;
use Zend\Form\FormInterface;

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
        parent::init();
    }

    public function init()
    {
        $this->add(
            array(
                'type'       => 'Select',
                'name'       => 'status',
                'options'    => array(
                    'value_options' => array(
                        'all' => /*@translate*/ 'All',
                        Status::ACTIVE => /*@translate*/ 'Active',
                        Status::INACTIVE => /*@translate*/ 'Inactive',
                        Status::WAITING_FOR_APPROVAL => /*@translate*/ 'Waiting for approval',
                        Status::CREATED => /*@translate*/ 'Created',
                        Status::PUBLISH => /*@translate*/ 'Published',
                        Status::REJECTED => /*@translate*/ 'Rejected',
                        Status::EXPIRED => /*@translate*/ 'Expired',
                    )
                ),
                'attributes' => array(
                    'value' => Status::WAITING_FOR_APPROVAL,
                )
            )
        );
    }
}
