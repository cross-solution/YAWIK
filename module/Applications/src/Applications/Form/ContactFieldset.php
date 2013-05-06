<?php

namespace Applications\Form;

use Applications\Model\Contact;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class ContactFieldset extends Fieldset implements InputFilterProviderInterface {

	public function __construct($model)
	{
		parent::__construct('application');
		
		$this->setHydrator(new ClassMethodsHydrator(false))
		->setObject(new Contact());
			
		$this->add(array(
				'type' => 'Zend\Form\Element\Radio',
				'name' => 'title',
				'options' => array(
						'label' => 'Title',
						'value_options' => array(
								'mister' => /*@translate*/ 'Mister',
								'miss' => /*@translate*/ 'Miss',
						),
				),
				'attributes' => array(
						'id' => 'contact-title',
						'title' => /*@translate*/ 'please choose your title'
				)
		));

		$this->add(array(
				'name' => 'firstname',
				'options' => array(
						'label' => /*@translate*/ 'First name'
				),
				'attributes' => array(
						'id' => 'contact-firstname',
						'title' => /*@translate*/ 'please enter your firstname'
				)
		));
		
		$this->add(array(
				'name' => 'lastname',
				'options' => array(
						'label' => /*@translate */'Last name'
				),
				'attributes' => array(
						'id' => 'contact-lastname',
						'title' => /*@translate */ 'please enter your lastname'
				)
		));
		
	}
	
	/**
	 * @return array
	 */
	public function getInputFilterSpecification()
	{
		return array(
				'name' => array(
						'required' => true,
				)
		);
	}
}

?>