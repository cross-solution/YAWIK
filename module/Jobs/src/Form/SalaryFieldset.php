<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Form;

use Jobs\Entity\Salary as SalaryEntity;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\Regex as RegexValidator;

/**
 * Defines the formular fields used in the formular for entering the job salary information
 *
 * @package Jobs\Form
 */
class SalaryFieldset extends Fieldset implements ViewPartialProviderInterface, InputFilterProviderInterface
{
    use ViewPartialProviderTrait;

    private $defaultPartial = 'jobs/form/salary-fieldset';

    public function init()
    {
        $this->setAttribute('id', 'jobsalary-fieldset');
        $this->setName('jobSalary');

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'value',
                'options' => array(
                    'label' => /*@translate*/ 'Salary',
                    'description' => /*@translate*/ 'Please enter the job salary amount',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Select',
                'name' => 'currency',
                'options' => array(
                    'label' => /*@translate*/ 'Currency',
                    'description' => /*@translate*/ 'Please enter the job salary currency',
                    'value_options' => array_map(function($val) {
                        return $val['name'];
                    }, SalaryEntity::getValidCurrencies()),
                )
            )
        );

        $this->add(
            array(
                'type' => 'Select',
                'name' => 'unit',
                'options' => array(
                    'label' => /*@translate*/ 'Time interval unit',
                    'description' => /*@translate*/ 'Please enter the job time interval unit',
                    'value_options' => array(
                        SalaryEntity::UNIT_HOUR => /*@translate*/ 'Hour',
                        SalaryEntity::UNIT_DAY => /*@translate*/ 'Day',
                        SalaryEntity::UNIT_WEEK => /*@translate*/ 'Week',
                        SalaryEntity::UNIT_MONTH => /*@translate*/ 'Month',
                        SalaryEntity::UNIT_YEAR => /*@translate*/ 'Year',

                    ),
                ),
                'attributes' => array(
                    'data-searchbox' => 'false',
                    'data-width' => '100%',
                    'value' => 'email',
                )
            )
        );
    }

    public function getInputFilterSpecification()
    {
        return array(
            'value' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    new RegexValidator('/^\$?[0-9]+(,[0-9]{3})*(.[0-9]{2})?$/'),
                ),
            ),
            'currency' => array(
                'required' => true,
                'filters' => array(),
                'validators' => array(
                    new InArrayValidator(array(
                        'haystack' => array_keys(SalaryEntity::getValidCurrencies()),
                        'strict' => true
                    )),
                ),
            ),
            'unit' => array(
                'required' => true,
                'filters' => array(),
                'validators' => array(
                    new InArrayValidator(array(
                        'haystack' => SalaryEntity::getValidUnits(),
                        'strict' => true
                    )),
                ),
            ),
        );
    }
}
