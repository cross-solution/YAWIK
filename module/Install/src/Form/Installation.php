<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Installation extends Form implements InputFilterProviderInterface
{

    public function init()
    {
        $this->setName('installation');
        $this->setAttribute('method', 'post');
        $this->add(array(
                       'type' => 'Text',
                       'name' => 'db_conn',
                       'options' => array(
                           'label' => /* @translate */ 'Database connection string',
                       ),

                   ));

        $this->add(array(
                       'type' => 'Text',
                       'name' => 'username',
                       'options' => array(
                           'label' => /* @translate */ 'Initial user name',
                       ),
                   ));

        $this->add(array(
                       'type' => 'Password',
                       'name' => 'password',
                       'options' => array(
                           'label' => /* @translate */ 'Password',
                       ),
                   ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'db_conn' => array(
                'required' => true,
                'validators' => array(
                    array('name' => 'Install/ConnectionString'),
                ),
            ),
        );
    }


}