<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Organizations forms */
namespace Organizations\Form;

// use Core\Form\SummaryForm;
// use Core\Form\Form;
use Core\Form\Container;

/**
 * Organization forms container 
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */

class Organizations extends Container {

    //protected $displayMode = self::DISPLAY_SUMMARY;

    public function init()
    {
        $this->setName('application-comment-form');
        //, 'options' => array('use_as_base_fieldset' => true)
        //$this->add(array('type' => 'Organizations/OrganizationsNameForm'))
        //    ->add(array('type' => 'Organizations/OrganizationContactForm'));
        //->add(array('type' => 'DefaultButtonsFieldset'));
        //;


        $this->setForms(array(
            'nameForm' => array(
                'type' => 'Organizations/OrganizationsNameForm',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Identify the company.',
                ),
            )
        ));

        $this->setForms(array(
            'locationForm' => array(
                'type' => 'Organizations/OrganizationsContactForm',
                'property' => 'contact',
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Basic Location Information.',
                ),
            )
        ));



    }

}