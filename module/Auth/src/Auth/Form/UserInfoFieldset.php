<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\CustomizableFieldsetTrait;
use Core\Form\EmptySummaryAwareInterface;
use Core\Form\EmptySummaryAwareTrait;
use Core\Form\CustomizableFieldsetInterface;
use Laminas\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\File;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class UserInfoFieldset extends Fieldset implements
    ViewPartialProviderInterface,
    EmptySummaryAwareInterface,
    InputFilterProviderInterface,
    CustomizableFieldsetInterface
{
    use EmptySummaryAwareTrait;
    use CustomizableFieldsetTrait;

    private $defaultEmptySummaryNotice = /*@translate*/ 'Click here to enter contact informations.';

    /**
     * View script for rendering
     *
     * @var string
     */
    protected $viewPartial = 'form/auth/contact';

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

    public function init()
    {
        $this->setName('info');

        $this->add(
            [
                'name'    => 'email',
                'options' => [
                    'label' => /* @translate */ 'Email'
                ],
                'attributes' => [
                    'required' => true, // marks the label as required.
                ]
            ]
        );

        $this->add(
            [
                'name'      => 'phone',
                'type'      => '\Core\Form\Element\Phone',
                'options'   => [
                    'label' => /* @translate */ 'Phone',
                ],
                'maxlength' => 20,
                'attributes' => [
                    'required' => true,
                ]
            ]
        );

        $this->add(
            [
                'name'    => 'postalCode',
                'options' => array(
                    'label' => /* @translate */ 'Postalcode'
                )
            ]
        );

        $this->add(
            [
                'name'    => 'city',
                'options' => [
                    'label' => /* @translate */ 'City'
                ]
            ]
        );

        $this->add(
            array(
                'name'       => 'gender',
                'type'       => 'Core\Form\Element\Select',
                'options'    => [
                    'label'         => /*@translate */ 'Salutation',
                    'value_options' => [
                        ''       => '',
                        'male'   => /*@translate */ 'Mr.',
                        'female' => /*@translate */ 'Mrs.',
                    ]
                ],
                'attributes' => [
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'false',
                    'data-searchbox' => -1,  // hide the search box
                    'required' => true, // mark label as required
                ],
            )
        );

        $this->add(
            array(
                'name'       => 'firstName',
                'required'   => true,
                'options'    => [
                    'label'     => /*@translate*/ 'First name',
                    'maxlength' => 50,
                ],
                'attributes' => [
                    'required' => true,
                ]
            )
        );

        $this->add(
            array(
                'name'     => 'lastName',
                'options'  => array(
                    'label'     => /*@translate*/ 'Last name',
                    'maxlength' => 50,
                ),
                'attributes' => [
                    'required' => true,
                ]
            )
        );

        $this->add(
            [
                'name'    => 'street',
                'options' => [
                    'label' => /*@translate*/ 'street'
                ]
            ]
        );

        $this->add(
            [
                'name'    => 'houseNumber',
                'options' => [
                    'label' => /*@translate*/ 'house number'
                ]
            ]
        );

        $this->add(
            [
                'name'    => 'country',
                'options' => [
                    'label' => /*@translate*/ 'country'
                ]
            ]
        );
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Laminas\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getDefaultInputFilterSpecification()
    {
        return array(
            'firstName' => array(
                'required'   => true,
                'filters'    => array(
                    array('name' => '\Laminas\Filter\StringTrim'),
                ),
                'validators' => array(
                    new NotEmpty(),
                    new StringLength(array('max' => 50))
                ),
            ),
            'lastName'  => array(
                'required'   => true,
                'filters'    => array(
                    array('name' => 'Laminas\Filter\StringTrim'),
                ),
                'validators' => array(
                    new NotEmpty(),
                    new StringLength(array('max' => 50))
                ),
            ),
            'email'     => array(
                'required'   => true,
                'filters'    => array(
                    array('name' => 'Laminas\Filter\StringTrim'),
                ),
                'validators' => array(
                    new NotEmpty(),
                    new StringLength(array('max' => 100)),
                    new EmailAddress()
                )
            ),
            'image'     => array(
                'required'   => false,
                'filters'    => array(),
                'validators' => array(
                    new File\Exists(),
                    new File\Extension(array('extension' => array('jpg', 'png', 'jpeg', 'gif'))),
                ),
            ),
        );
    }
}
