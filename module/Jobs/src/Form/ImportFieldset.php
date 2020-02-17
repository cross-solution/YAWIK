<?php

namespace Jobs\Form;

use Laminas\Form\Fieldset;
use Laminas\Validator\StringLength as StringLengthValidator;
use Laminas\Validator\EmailAddress as EmailAddressValidator;
use Laminas\Validator\ValidatorInterface;
use Laminas\InputFilter\InputFilterProviderInterface;
use Core\Repository\Hydrator;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;

/**
 * Class ImportFieldset
 *
 * @package Jobs\Form
 */

/**
 * Defines the formular fields which can be send via API calls.
 *
 * @package Jobs\Form
 */
class ImportFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }

        return $this->hydrator;
    }

    public function getInputFilterSpecification()
    {
        return [
            'company' => [
                'filters' => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'validators' => [
                    new StringLengthValidator(1),
                ],
            ],
            'title' => [
                'filters' => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'validators' => [
                    new StringLengthValidator(5),
                ],
            ],
            'link' => [
                'filters' => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'validators' => [
                    new StringLengthValidator(5),
                ],
            ],
            'contactEmail' => [
                'filters' => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'allow_empty' => true
            ],
            'datePublishStart' => [],
            'reference' => [
                'filters' => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'allow_empty' => true
            ],
            'atsEnabled' => [
                'filters' => [],
                'allow_empty' => true
            ],
            'logoRef' => [
                'filters' => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'allow_empty' => true
            ],
            'templateValues'       => [
                'filters'     => [],
                'allow_empty' => true
            ],
        ];
    }

    /**
     * defines the valid formular fields, which can be used via API
     */
    public function init()
    {
        $this->setName('job');
        $this->setAttribute('id', 'job');

        $this->add(
            [
                'type' => 'hidden',
                'name' => 'id'
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'applyId',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'company',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'title',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'link',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'location',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'contactEmail',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'datePublishStart',
            ]
        );

        $this->add(
            [
                'type' => 'Laminas\Form\Element\Text',
                'name' => 'reference',
            ]
        );

        $this->add(
            [
                'type'  => 'Laminas\Form\Element\Text',
                'name'  => 'logoRef',
            ]
        );

        $this->add(
            [
                'type' => 'Jobs/AtsModeFieldset',
            ]
        );
    }
}
