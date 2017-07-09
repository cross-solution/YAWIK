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
use Zend\InputFilter\InputFilterProviderInterface;
use Jobs\Form\Hydrator\TemplateLabelHydrator;

/**
 * Defines the formular field "requirements" of a job opening
 *
 * @package Jobs\Form
 */
class TemplateLabelRequirements extends Form implements InputFilterProviderInterface
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new TemplateLabelHydrator();
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
            [
                'type' => 'Text',
                'name' => 'description-label-requirements',
                'options' => [
                    'use_as_base_fieldset' => true,
                ]

            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return array(
            'description-label-requirements' => array(
                'filters' => array(
                    array(
                        'name' => 'Core/XssFilter'
                    )
                )
            )
       );
    }
}
