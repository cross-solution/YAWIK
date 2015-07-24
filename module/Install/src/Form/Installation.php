<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Installation form
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class Installation extends Form implements InputFilterProviderInterface
{

    public function init()
    {
        $this->setName('installation');
        $this->setAttributes(array(
                                 'method' => 'post',
                                 'action' => '?p=install'
                             )
        );

        $this->add(array(
                       'type'       => 'Text',
                       'name'       => 'db_conn',
                       'options'    => array(
                           'label' => /* @translate */ 'Database connection string',
                       ),
                       'attributes' => array(
                           'placeholder' => 'mongodb://localhost:27017/YAWIK',
                       ),

                   )
        );

        $this->add(array(
                       'type'    => 'Text',
                       'name'    => 'username',
                       'options' => array(
                           'label' => /* @translate */ 'Initial user name',
                       ),
                   )
        );

        $this->add(array(
                       'type'    => 'Password',
                       'name'    => 'password',
                       'options' => array(
                           'label' => /* @translate */ 'Password',
                       ),
                   )
        );
    }

    public function getInputFilterSpecification()
    {
        return array(
            'db_conn'  => array(
                'required'          => true,
                'continue_if_empty' => true,
                'validators'        => array(
                    array('name' => 'Install/ConnectionString'),
                ),
            ),
            'username' => array(
                'required' => true,
            ),
            'password' => array(
                'required' => true
            ),
        );
    }


}