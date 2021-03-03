<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;
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
        $this->setAttributes([
            'id' => 'jobs-form-qualifications',
            'data-handle-by' => 'yk-form'
        ]);

        $this->add([
            'type' => 'TextEditor',
            'name' => 'description-qualifications',
            'options' => [
                'use_as_base_fieldset' => true,
                'placeholder' => 'Qualifications'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'description-qualifications' => [
                'filters' => [
                    [
                        'name' => 'Core/XssFilter'
                    ],
                ],
                'allow_empty' => true,
            ],
        ];
    }
}
