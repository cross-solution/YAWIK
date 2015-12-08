<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
 * Defines the formular field "requirements" of a job opening
 *
 * @package Jobs\Form
 */
class JobDescriptionLabelRequirements extends Form implements InputFilterProviderInterface
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
        $this->setName('jobs-form-label-requirements');
        $this->setAttributes(
            array(
            'id' => 'jobs-form-label-requirements',
            'data-handle-by' => 'yk-form'
            )
        );

        $this->add(
            array(
            'type' => 'Text',
            'name' => 'description-label-requirements',
            'options' => array(
//                'use_as_base_fieldset' => true,
//                'placeholder' => 'Requirements'
            ),
            )
        );


    }

    public function getInputFilterSpecification()
    {
        return array(
            'description-label-requirements' => array(
                'filters' => array(
                    array(
                        'name' => 'Core\XssFilter'
                    )
                )
            )
       );
    }
}
