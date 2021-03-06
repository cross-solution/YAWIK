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
 * Defines the formular field "requirements" of a job opening
 *
 * @package Jobs\Form
 */
class JobDescriptionRequirements extends Form implements InputFilterProviderInterface
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
        $this->setName('jobs-form-requirements');
        $this->setAttributes([
            'id' => 'jobs-form-requirements',
            'data-handle-by' => 'yk-form'
        ]);

        $this->add([
            'type' => 'TextEditor',
            'name' => 'description-requirements',
            'options' => [
                'use_as_base_fieldset' => true,
                'placeholder' => 'Requirements'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'description-requirements' => [
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
