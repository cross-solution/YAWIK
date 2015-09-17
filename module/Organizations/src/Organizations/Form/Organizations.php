<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Organizations forms */
namespace Organizations\Form;

use Core\Form\Container;

/**
 * Organization form container
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Organizations extends Container
{

    public function init()
    {
        $this->setName('application-comment-form');

        $this->setForms(
            array(
            'nameForm' => array(
                'type' => 'Organizations/OrganizationsNameForm',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter the name of the hiring organization.',
                ),
            ),

            'locationForm' => array(
                'type' => 'Organizations/OrganizationsContactForm',
                'property' => 'contact',
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter a contact for the hiring organization.',
                ),
            ),

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

            'employeesManagement' => array(
                'type' => 'Organizations/Employees',
                'property' => true,
                'options' => array(
                    'label' => /*@translate*/ 'Employees',
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Manage your employees and their permissions.',
                ),
            ),

            )
        );
    }
}
