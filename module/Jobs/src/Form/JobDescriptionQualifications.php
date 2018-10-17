<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Jobs\Form\Hydrator\JobDescriptionHydrator;

/**
 * Defines the formular qualification of a job opening
 *
 * @package Jobs\Form
 */
class JobDescriptionQualifications extends Form implements InputFilterProviderInterface
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new JobDescriptionHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setName('jobs-form-qualifications');
        $this->setAttributes(
            array(
            'id' => 'jobs-form-qualifications',
            'data-handle-by' => 'yk-form'
            )
        );

        $this->add(
            array(
            'type' => 'TextEditor',
            'name' => 'description-qualifications',
            'options' => array(
                'use_as_base_fieldset' => true,
                'placeholder' => 'Qualifications'
            ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        return array(
            'description-qualifications' => array(
                'filters' => array(
                    array(
                        'name' => 'Core/XssFilter'
                    )
                )
            )
        );
    }
}
