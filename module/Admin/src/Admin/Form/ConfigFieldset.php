<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    Carsten Bleek bleek@cross-solution.de
 */

namespace Admin\Form;

use Zend\Form\Fieldset;


/**
 * Class ConfigFieldset
 * @package Admin\Form
 */
class ConfigFieldset extends Fieldset
{

    public function init()
    {
        $this->setName('name');

        $this->add(array(
            'name' => 'siteName',
            'options' => array(
                'label' => /* @translate */ 'Name of site',
                'description' => /* @translate */ 'enter the name of your site'
            )
        ));

        $this->add(array(
            'name' => 'imprintCompanyFullname',
            'options' => array(
                'label' => /* @translate */ 'Company Name',
                'description' => /*@translate*/ 'enter the name of your company. This name will appear in the imprint'
            )
        ));

        $this->add(array(
            'name' => 'imprintCompanyFullname',
            'options' => array(
                'label' => /* @translate */ 'Company Name (short)',
                'description' => /*@translate*/ 'The short name can be used in the terms and conditions. mails, etc.'
            )
        ));

        $this->add(array(
            'name' => 'imprintCompanyTax',
            'options' => array(
                'label' => /* @translate */ 'Tax Number',
                'description' => /*@translate*/ 'Tax Number is shown in the imprint'
            )
        ));

        $this->add(array(
            'name' => 'imprintCompanyFat',
            'options' => array(
                'label' => /* @translate */ 'Postalcode',
                'description' => /*@translate*/ 'This postal code is shown in the imprint'
            )
        ));

        $this->add(array(
            'name' => 'imprintCompanyCity',
            'options' => array(
                'label' => /* @translate */ 'City',
                'description' => /*@translate*/ 'This postal code is shown in the imprint'
            )
        ));

        $this->add(array(
            'name' => 'imprintPersonName',
            'options' => array(
                'label' => /* @translate */ 'Name',
                'description' => /*@translate*/ 'Please enter the name of the person who\'s responsible for this site. This name will appear in the imprint'
            )
        ));

        $this->add(array(
            'name' => 'imprintPersonPhone',
            'options' => array(
                'label' => /* @translate */ 'Name',
                'description' => /*@translate*/ 'Please enter a phone number of the person who\'s responsible for this site. This number will appear in the imprint'
            )
        ));
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }

    /**
     * @param object $object
     * @return $this|Fieldset|\Zend\Form\FieldsetInterface
     */
    public function setObject($object)
    {
        parent::setObject($object);
        return $this;
    }
}
