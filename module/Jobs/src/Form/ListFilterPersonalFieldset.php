<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license       MIT
 */

/** ListFilterLocationFieldset.php */
namespace Jobs\Form;

use Jobs\Entity\Status;
use Laminas\Form\Fieldset;
use Laminas\Form\FormInterface;

/**
 * Defines the formular fields of the job opening search formular
 *
 * @package Jobs\Form
 */
class ListFilterPersonalFieldset extends ListFilterBaseFieldset
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
                'type'       => 'Radio',
                'name'       => 'by',
                'options'    => array(
                    'value_options' => array(
                        'all' => /*@translate*/ 'Show all jobs',
                        'me'  => /*@translate*/ 'Show my jobs',
                    ),
                ),
                'attributes' => array(
                    'value' => 'all',
                )

            )
        );

        $this->add(
            array(
                'type'       => 'Radio',
                'name'       => 'status',
                'options'    => array(
                    'value_options' => array(
                        'all' => /*@translate*/ 'All',
                        Status::ACTIVE => /*@translate*/ 'Active',
                        Status::INACTIVE => /*@translate*/ 'Inactive',
                    )
                ),
                'attributes' => array(
                    'value' => 'all',
                )
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
