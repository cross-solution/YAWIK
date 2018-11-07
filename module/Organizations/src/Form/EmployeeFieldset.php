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

use Core\Form\ViewPartialProviderInterface;
use Organizations\Entity\EmployeeInterface;
use Zend\Form\Fieldset;
use Organizations\Entity\EmployeePermissionsInterface as Perms;

/**
 * An employee fieldset.
 *
 * This fieldset contains two elements:
 * A user reference (field of type Employee)
 * and the permissions multi checkboxes.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class EmployeeFieldset extends Fieldset implements ViewPartialProviderInterface
{
    protected $partial = 'organizations/form/employee-fieldset';

    public function setViewPartial($partial)
    {
        $this->partial = (string) $partial;

        return $this;
    }

    public function getViewPartial()
    {
        return $this->partial;
    }

    public function init()
    {
        $this->add(
            array(
            'type' => 'Organizations/Employee',
            'name' => 'user',
            )
        );


        $this->add(
            array(
            'type' => 'MultiCheckbox',
            'name' => 'permissions',
            'options' => array(
                'value_options' => array(
                    Perms::JOBS_VIEW => /*@translate*/ 'View Jobs',
                    Perms::JOBS_CHANGE => /*@translate*/ 'Edit Jobs',
                    Perms::JOBS_CREATE => /*@translate*/ 'Create Jobs',
                    Perms::APPLICATIONS_VIEW => /*@translate*/ 'View Applications',
                    Perms::APPLICATIONS_CHANGE => /*@translate*/ 'Edit Applications',
                ),
            ),
            )
        );

        $this->add(
            array(
                'type' => 'select',
                'name' => 'role',
                'options' => array(
                    'value_options' => array(
                        EmployeeInterface::ROLE_RECRUITER => /*@translate*/ 'Recruiter',
                        EmployeeInterface::ROLE_DEPARTMENT_MANAGER => /*@translate*/ 'Department Manager',
                        EmployeeInterface::ROLE_MANAGEMENT => /*@translate*/ 'Managing Directors',
                    ),
                ),
            )
        );

        $this->add(
            array(
            'type' => 'hidden',
            'name' => 'status',
            'attributes' => array(
                'value' => EmployeeInterface::STATUS_PENDING,
            ),
            )
        );
    }
}
