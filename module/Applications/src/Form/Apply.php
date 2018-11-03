<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications forms */
namespace Applications\Form;

use Core\Form\Container;

/**
 * Application forms container
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Apply extends Container
{
    
    /**
     * {@inheritDoc}
     *
     * Adds the standard forms and child containers.
     *
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setForms(
            array(
            'contact' => 'Applications/Contact',
            'base'    => array(
                'type' => 'Applications/Base',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Summary is meant as a general free text area. Click on "edit" to fill in some informations you think helps the recruiter to pick you for this job.',
                    'is_disable_elements_capable' => true,
                    'is_disable_capable' => false,
                ),
            ),
            'facts' => 'Applications/Facts',
            'profiles' => array(
                'type' => 'Auth/SocialProfiles',
                'options' => array(
                    'is_disable_capable' => true,
                    'is_disable_elements_capable' => true,
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'you can add your social profile to your application. You can preview and remove the attached profile before submitting the application.',
                ),
            ),
            'attachments' => 'Applications/Attachments',
            'attributes' => 'Applications/Attributes',
            )
        );

        /* This label is used on the Settings page */
        $this->options['settings_label'] = /*@translate*/ 'Customize apply form';
    }
}
