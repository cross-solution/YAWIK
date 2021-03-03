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
 * Defines the formular field benefits of a job opening.
 *
 * @package Jobs\Form
 */
class JobDescriptionBenefits extends Form implements InputFilterProviderInterface
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
        $this->setName('jobs-form-benefits');
        $this->setAttributes([
            'id' => 'jobs-form-benefits',
            'data-handle-by' => 'yk-form'
        ]);

        $this->add([
            'type' => 'TextEditor',
            'name' => 'description-benefits',
            'options' => [
                'use_as_base_fieldset' => true,
                'placeholder' => 'Benefits'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'description-benefits' => [
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => 'Core/XssFilter'
                    ],
                ],
            ],
        ];
    }
}
