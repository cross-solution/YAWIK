<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Zend\Validator\StringLength as StringLengthValidator;
use Zend\Validator\EmailAddress as EmailAddressValidator;
use Zend\Validator\ValidatorInterface;
use Zend\InputFilter\InputFilterProviderInterface;
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
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
                'validators' => [
                    new StringLengthValidator(1),
                ],
            ],
            'title' => [
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
                'validators' => [
                    new StringLengthValidator(5),
                ],
            ],
            'link' => [
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
                'validators' => [
                    new StringLengthValidator(5),
                ],
            ],
            'contactEmail' => [
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
                'allow_empty' => true
            ],
            'datePublishStart' => [],
            'reference' => [
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
                'allow_empty' => true
            ],
            'atsEnabled' => [
                'filters' => [],
                'allow_empty' => true
            ],
            'logoRef' => [
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
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
                'type' => 'Zend\Form\Element\Text',
                'name' => 'applyId',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'company',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'title',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'link',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'location',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'contactEmail',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'datePublishStart',
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Text',
                'name' => 'reference',
            ]
        );

        $this->add(
            [
                'type'  => 'Zend\Form\Element\Text',
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
