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
use Jobs\Entity\TemplateValuesInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Jobs\Form\Hydrator\JobDescriptionHydrator;

/**
 * Defines the formular field description of a job opening.
 *
 * @package Jobs\Form
 */
class JobDescriptionDescription extends Form implements InputFilterProviderInterface
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
        $this->setName('jobs-form-description');
        $this->setAttributes(
            array(
            'id' => 'jobs-form-description',
            'data-handle-by' => 'yk-form'
            )
        );

        $this->add(
            array(
            'type' => 'TextEditor',
            'name' => 'description-description',
            'options' => array(
                'use_as_base_fieldset' => true,
                'placeholder' => /*@translate*/ 'Enter optional company description here'
            ),
            )
        );


    }

    public function getInputFilterSpecification()
    {
        return array(
            'description-description' => array(
                'filters' => array(
                    array(
                        'name' => 'Core/XssFilter'
                    )
                )
            )
        );
    }
}
