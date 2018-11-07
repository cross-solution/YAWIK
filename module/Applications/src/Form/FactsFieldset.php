<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Applications\Form;

use Core\Form\DisableElementsCapableInterface;
use Zend\Form\Fieldset;
use Core\Form\EmptySummaryAwareInterface;

/**
 * Facts fieldset. Defines formular fields of the facts form fieldset.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FactsFieldset extends Fieldset implements DisableElementsCapableInterface, EmptySummaryAwareInterface
{
    /**
     * The empty summary notice.
     *
     * @var string
     */
    protected $emptySummaryNotice = /*@translate*/ 'Click here to enter facts.';

    /**
     * initialize facts fieldset
     */
    public function init()
    {
        $this->setHydrator(new \Core\Entity\Hydrator\EntityHydrator())
             ->setName('base');

        $this->add(
            array(
            'name' => 'willingnessToTravel',
            'type' => '\Core\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '' => '', // needed for jquery select2 to render the placeholder
                    "yes"=>/*@translate*/ "Yes",
                    "conditioned" => /*@translate*/ "conditioned",
                    "no"=>/*@translate*/ "No"),
                'label' => /*@translate*/ 'Willingness to travel',
                'description' => /*@translate*/ 'Enter your willingness to travel.',
                'disable_capable' => array(
                    'description' => /*@translate*/ 'Ask the applicant about the willingness to travel',
                ),
            ),
            'attributes' => array(
                'data-placeholder' => /*@translate*/ 'please select',
                'data-allowclear' => 'false',
                'data-searchbox' => -1,
                'data-width' => '100%'
            ),
            )
        );

        $this->add(
            [
                'name'       => 'earliestStartingDate',
                'type'       => 'Core/Datepicker',
                'options'    => [
                    'label'           => /*@translate*/ 'Earliest starting date',
                    'description'     => /*@translate*/ 'Enter the earliest starting date.',
                    'disable_capable' => [
                        'description' => /*@translate*/ 'Ask the applicant about the earliest starting date.',
                    ],
                ],
                'attributes' => [
                    'data-date-format' => 'yyyy-mm-dd',
                    'data-language' => 'de',
                    'class' => 'datepicker'
                ]
            ]
        );

        $this->add(
            array(
                'name'    => 'expectedSalary',
                'options' => array(
                    'label'           => /*@translate*/ 'Expected salary',
                    'description'     => /*@translate*/ 'Your salary requirements should be the annual amount before taxes. Do not forget to provide the currency sign.',
                    'disable_capable' => array(
                        'description' => /*@translate*/ 'Ask users about their expected salary.',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name'       => 'drivingLicense',
                'type'       => '\Core\Form\Element\Select',
                'options'    => array(
                    'value_options'   => array(
                        ''  => '', // needed for jquery select2 to render the placeholder
                        "1" =>/*@translate*/ "Yes",
                        "0" =>/*@translate*/ "No"
                    ),
                    'label'           => /*@translate*/ 'driving license',
                    'description'     => /*@translate*/ 'Do you have a driving license?',
                    'disable_capable' => array(
                        'description' => /*@translate*/ 'Ask the applicant, if he has a driving license.',
                    ),
                ),
                'attributes' => [
                    'data-allowclear'  => 'false',
                    'data-searchbox'   => -1,
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-width' => '100%'
                ]
            )
        );
    }

    /**
     * If all elements have empty values, the form will be displayed collapsed with the EmptySummaryNotice
     *
     * @return bool
     */
    public function isSummaryEmpty()
    {
        foreach ($this as $element) { /* @var $element \Zend\Form\ElementInterface */
            if ('' != $element->getValue()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setEmptySummaryNotice($message)
    {
        $this->emptySummaryNotice = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmptySummaryNotice()
    {
        return $this->emptySummaryNotice;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : true;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
            ? $this->options['is_disable_elements_capable']
            : true;
    }

    /**
     * Removes elements from Facts form.
     *
     * @param array $map
     * @return $this
     */
    public function disableElements(array $map)
    {
        foreach ($map as $element) {
            $this->remove($element);
        }
        return $this;
    }
}
