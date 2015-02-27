<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Form;

use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Fieldset;
use Organizations\Entity\EmployeePermissionsInterface as Perms;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
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
        $this->add(array(
            'type' => 'Organizations/Employee',
            'name' => 'user',
        ));


        $this->add(array(
            'type' => 'MultiCheckbox',
            'name' => 'permissions',
            'options' => array(
                'value_options' => array(
                    Perms::JOBS_VIEW => 'View Jobs',
                    Perms::JOBS_CHANGE => 'Edit Jobs',
                    Perms::JOBS_CREATE => 'Create Jobs',
                    Perms::APPLICATIONS_VIEW => 'View Applications',
                    Perms::APPLICATIONS_CHANGE => 'Edit Applications',
                ),
            ),
        ));
    }
    
}