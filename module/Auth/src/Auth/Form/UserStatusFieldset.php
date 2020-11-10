<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Auth\Form\Validator\UniqueLoginName;
use Core\Entity\Hydrator\EntityHydrator;
use Laminas\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Laminas\InputFilter\InputFilterProviderInterface;

class UserStatusFieldset extends Fieldset implements
    ViewPartialProviderInterface,
    InputFilterProviderInterface
{

    /**
     * View script for rendering
     *
     * @var string
     */
    protected $viewPartial = 'form/auth/status';

    /**
     * @var array
     */
    protected $statusOptions = [];

    /**
     * @param String $partial
     *
     * @return $this
     */
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewPartial()
    {
        return $this->viewPartial;
    }

    /**
     * @return \Laminas\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }

        return $this->hydrator;
    }

    /**
     * @param array $statusOptions
     * @return UserStatusFieldset
     */
    public function setStatusOptions(array $statusOptions)
    {
        $this->statusOptions = $statusOptions;

        return $this;
    }

    public function init()
    {
        $this->setName('status');

        $this->add(
            [
                'name'       => 'status',
                'type'       => 'Core\Form\Element\Select',
                'options'    => [
                    'label'         => /*@translate */ 'Status',
                    'value_options' => $this->statusOptions
                ],
                'attributes' => [
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'false',
                    'data-searchbox' => -1,  // hide the search box
                    'required' => true, // mark label as required
                ],
            ]
        );

        $this->add([
            'name' => 'login',
            'type' => 'text',
            'options' => [
                'label' => /* @translate */ 'Login name',
            ],

        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'login' => [
                'validators' => [
                    ['name' => UniqueLoginName::class],
                ],
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
            ]
        ];
    }
}
