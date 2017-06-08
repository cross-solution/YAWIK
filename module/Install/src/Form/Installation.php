<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Form;

use Install\Validator\MongoDbConnection;
use Install\Validator\MongoDbConnectionString;
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
	
	    $this->setAttributes(
		    array(
			    'method' => 'post',
		    )
	    );
	
	    $this->add(
		    array(
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
	
	    $this->add(
		    array(
			    'type'    => 'Text',
			    'name'    => 'username',
			    'options' => array(
				    'label' => /* @translate */ 'Initial user name',
			    ),
		    )
	    );
	
	    $this->add(
		    array(
			    'type'    => 'Password',
			    'name'    => 'password',
			    'options' => array(
				    'label' => /* @translate */ 'Password',
			    ),
		    )
	    );
	
	    $this->add(
		    array(
			    'type' => 'Text',
			    'name' => 'email',
			    'options' => array(
				    'label' => /* @translate */ 'Email address for system messages',
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
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators'        => array(
                    array('name' => MongoDbConnectionString::class,
                          'break_chain_on_failure' => true),
                    array('name' => MongoDbConnection::class),
                ),
            ),
            'username' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'password' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'email' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'EmailAddress'),
                ),
            ),
        );
    }
}
