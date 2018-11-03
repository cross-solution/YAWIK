<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Hydrator\Strategy\CollectionStrategy;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Fieldset;

/**
 * The employees fieldset used for managing employees.
 *
 * Currently has two elements:
 * A user search bar to add new employees and a Collection of EmployeeFieldsets (one for each employee).
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class EmployeesFieldset extends Fieldset implements ViewPartialProviderInterface
{
    protected $partial = 'organizations/form/employees-fieldset';

    public function setViewPartial($partial)
    {
        $this->partial = (string) $partial;

        return $this;
    }

    public function getViewPartial()
    {
        return $this->partial;
    }

    /**
     * Initializes the fieldset.
     */
    public function init()
    {
        $this->setName('employees');

        $this->add(
            array(
            'name' => 'inviteemployee',
            'type' => 'Organizations/InviteEmployeeBar',
            'options' => [
                'description' => /*@translate*/ 'Invite an employee via email address.',
            ]
            )
        );

        $this->add(
            array(

            'type' => 'Collection',
            'name' => 'employees',
            'options' => array(
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'allow_add' => true,
                'allow_remove' => true,
                'renderFieldset' => true,
                'target_element' => array(
                    'type' => 'Organizations/EmployeeFieldset'
                )
            ),
            )
        );
    }
}
