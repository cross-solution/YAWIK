<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Jobs forms */
namespace Jobs\Form;

use Core\Form\Container;

/**
 * Jobs forms container
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
class Job extends Container
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
        $this->setForms(array(
            'locationForm' => array(
                'type' => 'Jobs/TitleLocation',
                'property' => true,
            )
        ));

        /*
        $this->setForms(array(
            'employersForm' => array(
                'type' => 'Jobs/Employers',
                'property' => true,
            )
        ));
        */

        $this->setForms(array(
            'descriptionForm' => array(
                'type' => 'Jobs/Description',
                'property' => true,
            )
        ));

        // This label is used on the Settings page
        //$this->options['settings_label'] = /*@translate*/ 'Customize apply form';


    }

}
