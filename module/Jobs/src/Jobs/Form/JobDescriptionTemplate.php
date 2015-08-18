<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Jobs forms */
namespace Jobs\Form;

use Core\Form\Container;

/**
 * Compiles the formular fields of a job opening into a container
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
class JobDescriptionTemplate extends Container
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
            'descriptionFormDescription' => array(
                'type' => 'Jobs/JobDescriptionDescription',
                'property' => true,
            )
            )
        );

        $this->setForms(
            array(
            'descriptionFormBenefits' => array(
                'type' => 'Jobs/JobDescriptionBenefits',
                'property' => true,
            )
            )
        );

        $this->setForms(
            array(
            'descriptionFormRequirements' => array(
                'type' => 'Jobs/JobDescriptionRequirements',
                'property' => true,
            )
            )
        );

        $this->setForms(
            array(
            'descriptionFormQualifications' => array(
                'type' => 'Jobs/JobDescriptionQualifications',
                'property' => true,
            )
            )
        );

        $this->setForms(
            array(
            'descriptionFormTitle' => array(
                'type' => 'Jobs/JobDescriptionTitle',
                'property' => true,
            )
            )
        );

    }
}
