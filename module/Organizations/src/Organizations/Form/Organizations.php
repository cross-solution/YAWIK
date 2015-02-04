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
 * Organization form container
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */

class Organizations extends Container {

    //protected $displayMode = self::DISPLAY_SUMMARY;

    public function init()
    {
        $this->setName('application-comment-form');

        $this->setForms(array(
            'nameForm' => array(
                'type' => 'Organizations/OrganizationsNameForm',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter the name of the hiring organization.',
                ),
            )
        ));

        $this->setForms(array(
            'locationForm' => array(
                'type' => 'Organizations/OrganizationsContactForm',
                'property' => 'contact',
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter a contact for the hiring organization.',
                ),
            )
        ));

        $this->setForms(array(
            'organizationLogo' => array(
                'type' => 'Organizations/Image',
                'property' => true,
                'use_files_array' => true,
            ),
            'descriptionForm' => array(
                'type' => 'Organizations/OrganizationsDescriptionForm',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter a description for the hiring organization.',
                ),
            ),
        ));
    }
}